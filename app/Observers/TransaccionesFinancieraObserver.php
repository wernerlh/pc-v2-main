<?php

namespace App\Observers;

use App\Models\TransaccionesFinanciera;
use App\Services\BilleteraService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TransaccionesFinancieraObserver
{
    protected $billeteraService;

    public function __construct(BilleteraService $billeteraService)
    {
        $this->billeteraService = $billeteraService;
    }

    /**
     * Handle the TransaccionesFinanciera "created" event.
     */
    public function created(TransaccionesFinanciera $transaccion): void
    {
        try {
            // Usar una variable estática para evitar procesamiento duplicado en la misma ejecución
            static $procesadas = [];

            // Si ya procesamos esta transacción en esta ejecución, salir
            if (isset($procesadas[$transaccion->id])) {
                return;
            }

            // Marcar como procesada
            $procesadas[$transaccion->id] = true;

            Log::info('Nueva transacción financiera creada', [
                'transaccion_id' => $transaccion->id,
                'cliente_id' => $transaccion->cliente_id,
                'tipo' => $transaccion->tipo,
                'monto' => $transaccion->monto,
                'estado' => $transaccion->estado
            ]);

            // Si es un depósito pendiente, registrarlo en la billetera como pendiente
            if ($transaccion->tipo === 'deposito' && strtolower($transaccion->estado) === 'pendiente') {
                $this->billeteraService->registrarDepositoPendiente($transaccion);
            }

            // Si es un retiro pendiente
            if ($transaccion->tipo === 'retiro' && strtolower($transaccion->estado) === 'pendiente') {
                Log::info('Retiro pendiente registrado', ['transaccion_id' => $transaccion->id]);
            }
        } catch (\Exception $e) {
            Log::error('Error al procesar nueva transacción: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }
    /**
     * Handle the TransaccionesFinanciera "updating" event.
     * Se ejecuta antes de guardar los cambios en la base de datos.
     * Permite validar y modificar los datos antes de que se guarden.
     */
    public function updating(TransaccionesFinanciera $transaccion): bool
    {
        $cambios = $transaccion->getDirty();
        $estadoOriginal = $transaccion->getOriginal('estado');

        Log::info('Intentando actualizar transacción', [
            'transaccion_id' => $transaccion->id,
            'cambios' => $cambios,
            'estado_original' => $estadoOriginal,
            'estado_nuevo' => $transaccion->estado ?? $estadoOriginal
        ]);

        // Si se está cambiando el monto de una transacción ya existente (no permitido)
        if (isset($cambios['monto']) && $transaccion->id) {
            Log::warning('Intento de modificar monto de transacción', [
                'transaccion_id' => $transaccion->id,
                'monto_original' => $transaccion->getOriginal('monto'),
                'monto_nuevo' => $cambios['monto'],
                'usuario_id' => Auth::id() ?? 'sistema'
            ]);

            // Restauramos el monto original
            $transaccion->monto = $transaccion->getOriginal('monto');
        }

        // Si se está cambiando el tipo de transacción (depósito ↔ retiro)
        if (isset($cambios['tipo']) && $transaccion->id) {
            Log::warning('Intento de modificar tipo de transacción', [
                'transaccion_id' => $transaccion->id,
                'tipo_original' => $transaccion->getOriginal('tipo'),
                'tipo_nuevo' => $cambios['tipo'],
                'usuario_id' => Auth::id() ?? 'sistema'
            ]);

            // Restauramos el tipo original
            $transaccion->tipo = $transaccion->getOriginal('tipo');
        }

        // Si se está cambiando el cliente asociado a la transacción
        if (isset($cambios['cliente_id']) && $transaccion->id) {
            Log::warning('Intento de modificar cliente de transacción', [
                'transaccion_id' => $transaccion->id,
                'cliente_original' => $transaccion->getOriginal('cliente_id'),
                'cliente_nuevo' => $cambios['cliente_id'],
                'usuario_id' => Auth::id() ?? 'sistema'
            ]);

            // Restauramos el cliente original
            $transaccion->cliente_id = $transaccion->getOriginal('cliente_id');
        }

        // Validar transiciones de estado permitidas
        if (isset($cambios['estado'])) {
            $estadoNuevo = $transaccion->estado;

            // Transiciones no permitidas
            $transicionesInvalidas = [
                'completado' => ['rechazado'],      // No se puede pasar de completado a rechazado
                'completada' => ['rechazado'],      // Versión femenina
                'rechazado' => ['completado'],      // No se puede pasar de rechazado a completado
                'rechazada' => ['completado'],      // Versión femenina
                'cancelado' => ['completado', 'pendiente'], // No se puede reactivar una cancelación
                'cancelada' => ['completado', 'pendiente'], // Versión femenina
            ];

            if (
                isset($transicionesInvalidas[$estadoOriginal]) &&
                in_array($estadoNuevo, $transicionesInvalidas[$estadoOriginal])
            ) {

                Log::warning('Transición de estado no permitida', [
                    'transaccion_id' => $transaccion->id,
                    'estado_original' => $estadoOriginal,
                    'estado_nuevo' => $estadoNuevo,
                    'usuario_id' => Auth::id() ?? 'sistema'
                ]);

                // Restauramos el estado original
                $transaccion->estado = $estadoOriginal;
            }
        }

        // Permitir la actualización con los cambios validados
        return true;
    }

    /**
     * Handle the TransaccionesFinanciera "updated" event.
     */
    public function updated(TransaccionesFinanciera $transaccion): void
    {
        try {
            $estadoOriginal = $transaccion->getOriginal('estado');
            $estadoActual = $transaccion->estado;

            Log::info('Transacción actualizada', [
                'transaccion_id' => $transaccion->id,
                'cliente_id' => $transaccion->cliente_id,
                'tipo' => $transaccion->tipo,
                'monto' => $transaccion->monto,
                'estado_original' => $estadoOriginal,
                'estado_actual' => $estadoActual
            ]);

            // Solo procesar si el estado ha cambiado
            if ($estadoOriginal !== $estadoActual) {
                // Depósito completado - acreditar fondos al cliente
                if (
                    $transaccion->tipo === 'deposito' &&
                    (in_array(strtolower($estadoActual), ['completado', 'completada'])) &&
                    in_array(strtolower($estadoOriginal), ['pendiente', 'en_revision'])
                ) {

                    Log::info('Procesando depósito completado', ['transaccion_id' => $transaccion->id]);
                    $this->billeteraService->procesarDepositoCompletado($transaccion);
                    Log::info('Depósito completado procesado', ['transaccion_id' => $transaccion->id]);
                }

                // Depósito rechazado - actualizar registros pendientes
                if (
                    $transaccion->tipo === 'deposito' &&
                    (in_array(strtolower($estadoActual), ['rechazado', 'rechazada'])) &&
                    in_array(strtolower($estadoOriginal), ['pendiente', 'en_revision'])
                ) {

                    $this->billeteraService->procesarDepositoRechazado($transaccion);
                    Log::info('Depósito rechazado procesado', ['transaccion_id' => $transaccion->id]);
                }

                // Retiro completado - actualizar registros de billetera
                if (
                    $transaccion->tipo === 'retiro' &&
                    (in_array(strtolower($estadoActual), ['completado', 'completada'])) &&
                    in_array(strtolower($estadoOriginal), ['pendiente', 'en_revision'])
                ) {

                    $this->billeteraService->procesarRetiroCompletado($transaccion);
                    Log::info('Retiro completado procesado', ['transaccion_id' => $transaccion->id]);
                }

                // Retiro rechazado - devolver fondos al cliente
                if (
                    $transaccion->tipo === 'retiro' &&
                    (in_array(strtolower($estadoActual), ['rechazado', 'rechazada', 'cancelado', 'cancelada'])) &&
                    in_array(strtolower($estadoOriginal), ['pendiente', 'en_revision'])
                ) {

                    $this->billeteraService->liberarFondosReservados($transaccion);
                    Log::info('Fondos de retiro rechazado liberados', ['transaccion_id' => $transaccion->id]);
                }
            }

            // Actualizar metadatos si el estado cambió a completado o rechazado
            $this->actualizarMetadatos($transaccion, $estadoOriginal, $estadoActual);
        } catch (\Exception $e) {
            Log::error('Error al procesar la actualización de transacción: ' . $e->getMessage(), [
                'transaccion_id' => $transaccion->id,
                'exception' => $e
            ]);
        }
    }

    /**
     * Añadir metadatos para auditoría cuando una transacción es completada o rechazada
     */
    protected function actualizarMetadatos(TransaccionesFinanciera $transaccion, string $estadoOriginal, string $estadoActual): void
    {
        $estadosFinales = ['completado', 'completada', 'rechazado', 'rechazada', 'cancelado', 'cancelada'];

        // Si pasó a un estado final y viene de un estado no final
        if (in_array(strtolower($estadoActual), $estadosFinales) && !in_array(strtolower($estadoOriginal), $estadosFinales)) {
            $actualizaciones = [];

            // Si no tiene fecha de procesamiento, añadirla
            if (!$transaccion->fecha_procesamiento) {
                $actualizaciones['fecha_procesamiento'] = now();
            }

            // Si no tiene procesador asignado y hay un usuario autenticado
            if (!$transaccion->procesado_por && Auth::check()) {
                $actualizaciones['procesado_por'] = Auth::id();
            }

            // Si hay actualizaciones que hacer
            if (!empty($actualizaciones)) {
                $transaccion->fill($actualizaciones);
                // Guardamos sin disparar eventos para evitar un bucle infinito
                $transaccion->saveQuietly();

                Log::info('Metadatos de procesamiento actualizados', [
                    'transaccion_id' => $transaccion->id,
                    'actualizaciones' => $actualizaciones
                ]);
            }
        }
    }

    /**
     * Handle the TransaccionesFinanciera "deleting" event.
     * Esta función previene la eliminación accidental de transacciones
     */
    public function deleting(TransaccionesFinanciera $transaccion): bool
    {
        // En un sistema financiero real, no se deberían eliminar transacciones
        // Registramos el intento y prevenimos la eliminación
        Log::warning('Intento de eliminar transacción financiera', [
            'transaccion_id' => $transaccion->id,
            'usuario_id' => Auth::id() ?? 'sistema',
            'tipo' => $transaccion->tipo,
            'monto' => $transaccion->monto,
            'estado' => $transaccion->estado,
        ]);

        // Devolvemos false para prevenir la eliminación
        return false;
    }

    /**
     * Handle the TransaccionesFinanciera "restored" event.
     * Para cuando se usa soft deletes
     */
    public function restored(TransaccionesFinanciera $transaccion): void
    {
        // Si la aplicación utiliza soft-deletes y se restaura una transacción,
        // registramos el evento
        Log::info('Transacción financiera restaurada', [
            'transaccion_id' => $transaccion->id,
            'tipo' => $transaccion->tipo,
            'monto' => $transaccion->monto,
            'estado' => $transaccion->estado,
            'usuario_id' => Auth::id() ?? 'sistema',
        ]);
    }
}
