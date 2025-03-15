<?php

namespace App\Services;

use App\Models\BilleteraCliente;
use App\Models\TransaccionesFinanciera;
use App\Models\UserCliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BilleteraService
{
    /**
     * Obtiene o crea una billetera para un cliente
     */
    public function obtenerOCrearBilletera(int $clienteId): BilleteraCliente
    {
        $billetera = BilleteraCliente::where('cliente_id', $clienteId)->first();

        if (!$billetera) {
            $billetera = new BilleteraCliente();
            $billetera->cliente_id = $clienteId;
            $billetera->balance_real = 0;
            $billetera->balance_rechazadas = 0;
            $billetera->balance_pendiente = 0;
            $billetera->total_depositado = 0;
            $billetera->total_retirado = 0;
            $billetera->total_ganado = 0;
            $billetera->total_apostado = 0;
            $billetera->moneda = 'PEN';
            $billetera->save();

            Log::info('Nueva billetera creada', [
                'cliente_id' => $clienteId,
            ]);
        }

        return $billetera;
    }

    /**
     * Registra un depósito pendiente en la billetera
     */
    public function registrarDepositoPendiente(TransaccionesFinanciera $transaccion): void
    {
        try {
            DB::beginTransaction();
    
            // Debug log para rastrear el flujo
            Log::debug('Intentando registrar depósito pendiente', [
                'transaccion_id' => $transaccion->id,
                'estado_actual' => $transaccion->estado,
                'monto' => $transaccion->monto
            ]);
    
            // Utilizamos un campo de metadatos para verificar si ya fue procesado
            // Podemos usar un campo que no afecte la funcionalidad como "fecha_procesamiento"
            $yaRegistrada = $transaccion->fecha_procesamiento !== null;
                
            if ($yaRegistrada) {
                // Si ya tiene fecha de procesamiento, asumimos que ya fue procesada
                DB::commit();
                Log::info('Depósito pendiente ya procesado previamente, no se modifica balance', [
                    'transaccion_id' => $transaccion->id
                ]);
                return;
            }
    
            $billetera = $this->obtenerOCrearBilletera($transaccion->cliente_id);
            
            // Guardar valor anterior para logging
            $balance_pendiente_anterior = $billetera->balance_pendiente;
    
            // El balance_pendiente se incrementa para llevar un control
            $billetera->balance_pendiente += $transaccion->monto;
            $billetera->save();
    
            // Marcar como procesada usando fecha_procesamiento
            $transaccion->fecha_procesamiento = now();
            $transaccion->saveQuietly(); // Usar saveQuietly para evitar disparar eventos nuevamente
    
            DB::commit();
    
            Log::info('Depósito pendiente registrado exitosamente', [
                'cliente_id' => $transaccion->cliente_id,
                'monto' => $transaccion->monto,
                'balance_pendiente_anterior' => $balance_pendiente_anterior,
                'balance_pendiente_nuevo' => $billetera->balance_pendiente,
                'transaccion_id' => $transaccion->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar depósito pendiente: ' . $e->getMessage(), [
                'transaccion_id' => $transaccion->id,
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Procesa un depósito completado, actualizando el balance real
     */
    public function procesarDepositoCompletado(TransaccionesFinanciera $transaccion): void
    {
        try {
            DB::beginTransaction();

            $billetera = $this->obtenerOCrearBilletera($transaccion->cliente_id);

            // Guardar valores antiguos para logging
            $balance_real_anterior = $billetera->balance_real;
            $balance_pendiente_anterior = $billetera->balance_pendiente;
            $total_depositado_anterior = $billetera->total_depositado;

            // Aumentar el balance real con el monto del depósito
            $billetera->balance_real += $transaccion->monto;

            // Actualizar el total depositado (estadísticas)
            $billetera->total_depositado += $transaccion->monto;

            // Verificar si esta transacción específica estaba previamente en estado pendiente
            $estadoAnterior = DB::table('transacciones_financieras')
                ->where('id', $transaccion->id)
                ->value('estado');
                
            $estabaPendiente = strtolower($estadoAnterior) === 'pendiente';

            // Siempre intentar restar del balance pendiente si hay fondos pendientes
            if ($billetera->balance_pendiente > 0) {
                // Restar como máximo el monto de la transacción o el balance pendiente actual
                $montoARestar = min($transaccion->monto, $billetera->balance_pendiente);
                $billetera->balance_pendiente = max(0, $billetera->balance_pendiente - $montoARestar);
            }

            $billetera->save();

            DB::commit();

            Log::info('Depósito completado procesado correctamente', [
                'cliente_id' => $transaccion->cliente_id,
                'monto' => $transaccion->monto,
                'transaccion_id' => $transaccion->id,
                'estado_anterior' => $estadoAnterior,
                'estaba_pendiente' => $estabaPendiente,
                'balance_real_anterior' => $balance_real_anterior,
                'balance_real_nuevo' => $billetera->balance_real,
                'balance_pendiente_anterior' => $balance_pendiente_anterior,
                'balance_pendiente_nuevo' => $billetera->balance_pendiente,
                'total_depositado_anterior' => $total_depositado_anterior,
                'total_depositado_nuevo' => $billetera->total_depositado
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar depósito completado: ' . $e->getMessage(), [
                'transaccion_id' => $transaccion->id,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    /**
     * Procesa un depósito rechazado, actualizando registros pendientes
     * y moviendo el monto a balance_rechazadas
     */
    public function procesarDepositoRechazado(TransaccionesFinanciera $transaccion): void
    {
        try {
            DB::beginTransaction();

            $billetera = $this->obtenerOCrearBilletera($transaccion->cliente_id);

            // Guardar valores antiguos para logging
            $balance_pendiente_anterior = $billetera->balance_pendiente;
            $balance_rechazadas_anterior = $billetera->balance_rechazadas;

            // Verificar si esta transacción específica estaba previamente en estado pendiente
            $estadoAnterior = DB::table('transacciones_financieras')
                ->where('id', $transaccion->id)
                ->value('estado');
                
            $estabaPendiente = strtolower($estadoAnterior) === 'pendiente';

            // Solo procesar si el depósito estaba en pendiente o si hay balance pendiente
            if (($estabaPendiente || $billetera->balance_pendiente > 0) && $transaccion->monto > 0) {
                // Restar del pendiente solo si estaba pendiente
                if ($estabaPendiente && $billetera->balance_pendiente > 0) {
                    $montoARestar = min($transaccion->monto, $billetera->balance_pendiente);
                    $billetera->balance_pendiente = max(0, $billetera->balance_pendiente - $montoARestar);
                }

                // Añadir al balance_rechazadas para tener un historial visual de los rechazos
                $billetera->balance_rechazadas += $transaccion->monto;

                $billetera->save();

                Log::info('Depósito rechazado procesado', [
                    'cliente_id' => $transaccion->cliente_id,
                    'monto' => $transaccion->monto,
                    'estado_anterior' => $estadoAnterior,
                    'estaba_pendiente' => $estabaPendiente,
                    'balance_pendiente_anterior' => $balance_pendiente_anterior,
                    'balance_pendiente_nuevo' => $billetera->balance_pendiente,
                    'balance_rechazadas_anterior' => $balance_rechazadas_anterior,
                    'balance_rechazadas_nuevo' => $billetera->balance_rechazadas,
                    'transaccion_id' => $transaccion->id
                ]);
            } else {
                Log::info('Depósito rechazado ignorado (no estaba pendiente)', [
                    'cliente_id' => $transaccion->cliente_id,
                    'monto' => $transaccion->monto,
                    'transaccion_id' => $transaccion->id,
                    'estado_anterior' => $estadoAnterior,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar depósito rechazado: ' . $e->getMessage(), [
                'transaccion_id' => $transaccion->id,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    /**
     * Verifica si un cliente puede retirar un monto específico
     */
    public function puedeRetirar(int $clienteId, float $monto): bool
    {
        $billetera = $this->obtenerOCrearBilletera($clienteId);
        return $billetera->balance_real >= $monto;
    }

    /**
     * Reserva fondos para un retiro pendiente
     */
    public function reservarFondosParaRetiro(int $clienteId, float $monto): void
    {
        try {
            DB::beginTransaction();

            $billetera = $this->obtenerOCrearBilletera($clienteId);

            // Verificar saldo suficiente
            if ($billetera->balance_real < $monto) {
                throw new \Exception('Saldo insuficiente para retiro');
            }

            // Guardar valores antiguos para logging
            $balance_real_anterior = $billetera->balance_real;
            $balance_pendiente_anterior = $billetera->balance_pendiente;

            // Reducir el saldo disponible
            $billetera->balance_real -= $monto;
            // Añadir al saldo pendiente (para tracking)
            $billetera->balance_pendiente += $monto;
            $billetera->save();

            DB::commit();

            Log::info('Fondos reservados para retiro', [
                'cliente_id' => $clienteId,
                'monto' => $monto,
                'balance_real_anterior' => $balance_real_anterior,
                'balance_real_nuevo' => $billetera->balance_real,
                'balance_pendiente_anterior' => $balance_pendiente_anterior,
                'balance_pendiente_nuevo' => $billetera->balance_pendiente
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al reservar fondos para retiro: ' . $e->getMessage(), [
                'cliente_id' => $clienteId,
                'monto' => $monto,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    /**
     * Procesa un retiro completado, actualizando el total retirado
     */
    public function procesarRetiroCompletado(TransaccionesFinanciera $transaccion): void
    {
        try {
            DB::beginTransaction();

            $billetera = $this->obtenerOCrearBilletera($transaccion->cliente_id);

            // Guardar valores antiguos para logging
            $balance_pendiente_anterior = $billetera->balance_pendiente;
            $total_retirado_anterior = $billetera->total_retirado;

            // Obtener estado anterior
            $estadoAnterior = DB::table('transacciones_financieras')
                ->where('id', $transaccion->id)
                ->value('estado');
                
            $estabaPendiente = strtolower($estadoAnterior) === 'pendiente';

            // Actualizar el total retirado
            $billetera->total_retirado += $transaccion->monto;

            // Reducir el balance pendiente solo si esta transacción estaba pendiente
            if ($estabaPendiente && $billetera->balance_pendiente > 0) {
                $montoARestar = min($transaccion->monto, $billetera->balance_pendiente);
                $billetera->balance_pendiente = max(0, $billetera->balance_pendiente - $montoARestar);
            }

            $billetera->save();

            DB::commit();

            Log::info('Retiro completado procesado correctamente', [
                'cliente_id' => $transaccion->cliente_id,
                'monto' => $transaccion->monto,
                'transaccion_id' => $transaccion->id,
                'estado_anterior' => $estadoAnterior,
                'estaba_pendiente' => $estabaPendiente,
                'balance_pendiente_anterior' => $balance_pendiente_anterior,
                'balance_pendiente_nuevo' => $billetera->balance_pendiente,
                'total_retirado_anterior' => $total_retirado_anterior,
                'total_retirado_nuevo' => $billetera->total_retirado
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar retiro completado: ' . $e->getMessage(), [
                'transaccion_id' => $transaccion->id,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    /**
     * Libera fondos reservados cuando se rechaza un retiro
     */
    public function liberarFondosReservados(TransaccionesFinanciera $transaccion): void
    {
        try {
            DB::beginTransaction();

            $billetera = $this->obtenerOCrearBilletera($transaccion->cliente_id);

            // Guardar valores antiguos para logging
            $balance_real_anterior = $billetera->balance_real;
            $balance_pendiente_anterior = $billetera->balance_pendiente;

            // Obtener estado anterior
            $estadoAnterior = DB::table('transacciones_financieras')
                ->where('id', $transaccion->id)
                ->value('estado');
                
            $estabaPendiente = strtolower($estadoAnterior) === 'pendiente';

            // Solo procesar si el retiro estaba pendiente o si hay balance pendiente
            if ($estabaPendiente || $billetera->balance_pendiente > 0) {
                // Devolver el monto al balance real
                $billetera->balance_real += $transaccion->monto;
                
                // Quitar del balance pendiente solo si estaba en pendiente
                if ($estabaPendiente && $billetera->balance_pendiente > 0) {
                    $montoARestar = min($transaccion->monto, $billetera->balance_pendiente);
                    $billetera->balance_pendiente = max(0, $billetera->balance_pendiente - $montoARestar);
                }
                
                $billetera->save();

                Log::info('Fondos liberados por retiro rechazado', [
                    'cliente_id' => $transaccion->cliente_id,
                    'monto' => $transaccion->monto,
                    'transaccion_id' => $transaccion->id,
                    'estado_anterior' => $estadoAnterior,
                    'estaba_pendiente' => $estabaPendiente,
                    'balance_real_anterior' => $balance_real_anterior,
                    'balance_real_nuevo' => $billetera->balance_real,
                    'balance_pendiente_anterior' => $balance_pendiente_anterior,
                    'balance_pendiente_nuevo' => $billetera->balance_pendiente
                ]);
            } else {
                Log::info('Retiro rechazado ignorado (no estaba pendiente)', [
                    'cliente_id' => $transaccion->cliente_id,
                    'monto' => $transaccion->monto,
                    'transaccion_id' => $transaccion->id,
                    'estado_anterior' => $estadoAnterior
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al liberar fondos de retiro: ' . $e->getMessage(), [
                'transaccion_id' => $transaccion->id,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    /**
     * Registra una apuesta realizada
     */
    public function registrarApuesta(int $clienteId, float $monto): bool
    {
        try {
            DB::beginTransaction();

            $billetera = $this->obtenerOCrearBilletera($clienteId);
            /*
            // Verificar límite diario de apuestas
            $cliente = UserCliente::find($clienteId);
            $limite = $cliente->limite_apuesta_diario ?? 1000;
            
            $apuestasHoy = $this->obtenerApuestasDiarias($clienteId);
            if (($apuestasHoy + $monto) > $limite) {
                Log::warning('Límite diario de apuestas excedido', [
                    'cliente_id' => $clienteId,
                    'apuestas_hoy' => $apuestasHoy,
                    'monto_nuevo' => $monto,
                    'limite' => $limite
                ]);
                DB::rollBack();
                return false;
            }
            */

            // Verificar saldo suficiente
            if ($billetera->balance_real < $monto) {
                Log::warning('Saldo insuficiente para apuesta', [
                    'cliente_id' => $clienteId,
                    'balance_real' => $billetera->balance_real,
                    'monto_apuesta' => $monto
                ]);
                DB::rollBack();
                return false;
            }

            // Reducir saldo y actualizar total apostado
            $billetera->balance_real -= $monto;
            $billetera->total_apostado += $monto;
            $billetera->save();

            DB::commit();

            Log::info('Apuesta registrada', [
                'cliente_id' => $clienteId,
                'monto' => $monto,
                'balance_actual' => $billetera->balance_real,
                'total_apostado' => $billetera->total_apostado
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar apuesta: ' . $e->getMessage(), [
                'cliente_id' => $clienteId,
                'monto' => $monto,
                'exception' => $e
            ]);
            return false;
        }
    }

    /**
     * Registra una ganancia de apuesta
     */
    public function registrarGanancia(int $clienteId, float $monto): void
    {
        try {
            DB::beginTransaction();

            $billetera = $this->obtenerOCrearBilletera($clienteId);

            // Actualizar balance y estadísticas
            $billetera->balance_real += $monto;
            $billetera->total_ganado += $monto;
            $billetera->save();

            DB::commit();

            Log::info('Ganancia registrada', [
                'cliente_id' => $clienteId,
                'monto' => $monto,
                'balance_actual' => $billetera->balance_real,
                'total_ganado' => $billetera->total_ganado
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar ganancia: ' . $e->getMessage(), [
                'cliente_id' => $clienteId,
                'monto' => $monto,
                'exception' => $e
            ]);
            throw $e;
        }
    }

    /**
     * Obtiene el total de apuestas realizadas hoy por un cliente
     */
    private function obtenerApuestasDiarias(int $clienteId): float
    {
        // Esta implementación es simplificada. En un sistema real,
        // deberías tener una tabla de transacciones de juego para calcular esto.
        // Por ahora, simplemente devolvemos 0 para permitir apuestas.
        return 0;
    }

    /**
     * Verifica si un cliente puede depositar un monto específico
     */
    public function puedeDepositar(int $clienteId, float $monto): bool
    {
        //$cliente = UserCliente::find($clienteId);
        //$limite = $cliente->limite_deposito_diario ?? 1000;

        //$depositosHoy = $this->obtenerDepositosDiarios($clienteId);
        ///return ($depositosHoy + $monto) <= $limite;
        return true; // Siempre permite depositar
    }

    /**
     * Obtiene el total de depósitos realizados hoy por un cliente
     */
    private function obtenerDepositosDiarios(int $clienteId): float
    {
        $hoy = now()->startOfDay();
        $manana = now()->addDay()->startOfDay();

        $totalHoy = TransaccionesFinanciera::where('cliente_id', $clienteId)
            ->where('tipo', 'deposito')
            ->whereIn('estado', ['completado', 'completada', 'pendiente'])
            ->whereBetween('fecha_solicitud', [$hoy, $manana])
            ->sum('monto');

        return (float) $totalHoy;
    }

    /**
     * Método para depuración: muestra el estado actual de la billetera
     */
    public function diagnosticarBilletera(int $clienteId): array
    {
        $billetera = $this->obtenerOCrearBilletera($clienteId);

        $datos = [
            'cliente_id' => $billetera->cliente_id,
            'balance_real' => $billetera->balance_real,
            'balance_rechazadas' => $billetera->balance_rechazadas,
            'balance_pendiente' => $billetera->balance_pendiente,
            'total_depositado' => $billetera->total_depositado,
            'total_retirado' => $billetera->total_retirado,
            'total_ganado' => $billetera->total_ganado,
            'total_apostado' => $billetera->total_apostado
        ];

        Log::info('Diagnóstico de billetera', $datos);
        return $datos;
    }

    /**
     * Obtiene un resumen de la actividad reciente de la billetera
     */
    public function obtenerResumenActividad(int $clienteId): array
    {
        $cliente = UserCliente::find($clienteId);
        $billetera = $this->obtenerOCrearBilletera($clienteId);

        $ultimosDepositos = TransaccionesFinanciera::where('cliente_id', $clienteId)
            ->where('tipo', 'deposito')
            ->orderBy('fecha_solicitud', 'desc')
            ->limit(5)
            ->get();

        $ultimosRetiros = TransaccionesFinanciera::where('cliente_id', $clienteId)
            ->where('tipo', 'retiro')
            ->orderBy('fecha_solicitud', 'desc')
            ->limit(5)
            ->get();

        return [
            'cliente' => $cliente,
            'billetera' => $billetera,
            'ultimosDepositos' => $ultimosDepositos,
            'ultimosRetiros' => $ultimosRetiros
        ];
    }

    /**
     * Añadir método para actualizar manualmente el balance pendiente
     * Útil para corregir inconsistencias
     */
    public function actualizarBalancePendiente(int $clienteId, float $nuevoBalancePendiente): void
    {
        try {
            DB::beginTransaction();

            $billetera = $this->obtenerOCrearBilletera($clienteId);
            $valorAnterior = $billetera->balance_pendiente;
            
            $billetera->balance_pendiente = $nuevoBalancePendiente;
            $billetera->save();
            
            DB::commit();
            
            Log::info('Balance pendiente actualizado manualmente', [
                'cliente_id' => $clienteId,
                'balance_pendiente_anterior' => $valorAnterior,
                'balance_pendiente_nuevo' => $nuevoBalancePendiente
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar balance pendiente: ' . $e->getMessage(), [
                'cliente_id' => $clienteId,
                'exception' => $e
            ]);
            throw $e;
        }
    }
}