<?php

namespace App\Http\Controllers;

use App\Models\BilleteraCliente;
use App\Models\TransaccionesFinanciera;
use App\Models\UserCliente;
use App\Services\BilleteraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FinancieroController extends Controller
{
    protected $billeteraService;
    
    public function __construct(BilleteraService $billeteraService)
    {
        $this->billeteraService = $billeteraService;
    }
    
    /**
     * Registra una solicitud de depósito del cliente
     */
    public function solicitarDeposito(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'monto' => 'required|numeric|min:1',
            'banco' => 'required|string',
            'numero_cuenta_bancaria' => 'required|string',
            'titular_cuenta' => 'required|string',
            'referencia_transferencia' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Obtener el cliente actual
        $cliente = UserCliente::find(Auth::id());
        
        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }
        
        try {
            DB::beginTransaction();
            
            // Crear la transacción de depósito
            $transaccion = TransaccionesFinanciera::create([
                'cliente_id' => $cliente->id,
                'monto' => $request->monto,
                'tipo' => 'deposito',
                'estado' => 'pendiente',
                'banco' => $request->banco,
                'numero_cuenta_bancaria' => $request->numero_cuenta_bancaria,
                'titular_cuenta' => $request->titular_cuenta,
                'referencia_transferencia' => $request->referencia_transferencia,
                'fecha_solicitud' => now(),
            ]);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Solicitud de depósito registrada correctamente',
                'transaccion' => $transaccion
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al registrar el depósito: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Registra una solicitud de retiro del cliente
     */
    public function solicitarRetiro(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'monto' => 'required|numeric|min:1',
            'banco' => 'required|string',
            'numero_cuenta_bancaria' => 'required|string',
            'titular_cuenta' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Obtener el cliente actual
        $cliente = UserCliente::find(Auth::id());
        
        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }
        
        // Verificar si tiene saldo suficiente
        if (!$cliente->billetera->puedeRetirar($request->monto)) {
            return response()->json(['error' => 'Saldo insuficiente para realizar el retiro'], 400);
        }
        
        try {
            DB::beginTransaction();
            
            // Crear la transacción de retiro
            $transaccion = TransaccionesFinanciera::create([
                'cliente_id' => $cliente->id,
                'monto' => $request->monto,
                'tipo' => 'retiro',
                'estado' => 'pendiente',
                'banco' => $request->banco,
                'numero_cuenta_bancaria' => $request->numero_cuenta_bancaria,
                'titular_cuenta' => $request->titular_cuenta,
                'fecha_solicitud' => now(),
            ]);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Solicitud de retiro registrada correctamente',
                'transaccion' => $transaccion
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al registrar el retiro: ' . $e->getMessage()], 500);
        }
    }
}