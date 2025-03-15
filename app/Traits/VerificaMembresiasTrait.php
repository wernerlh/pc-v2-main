<?php

namespace App\Traits;

use App\Models\ClienteMembresia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait VerificaMembresiasTrait
{
    /**
     * Verifica y actualiza el estado de las membresías vencidas del usuario actual.
     * 
     * @return void
     */
    protected function verificarMembresiasVencidas(): void
    {
        $userId = Auth::guard('cliente')->id();

        if (!$userId) {
            return;
        }

        // Actualizar el estado de las membresías vencidas del usuario actual
        ClienteMembresia::where('cliente_id', $userId)
            ->where('estado', 'activa')
            ->where('fecha_vencimiento', '<', Carbon::now())
            ->update(['estado' => 'vencida']);
    }
    
    /**
     * Verifica si el usuario tiene acceso a un juego específico según su membresía.
     * 
     * @param int|null $membresia_requerida El ID de la membresía requerida para el juego
     * @return bool
     */
    protected function verificarAccesoJuego(?int $membresia_requerida): bool
    {
        if (!$membresia_requerida) {
            return true; // Si no requiere membresía, todos tienen acceso
        }
        
        $userId = Auth::guard('cliente')->id();
        
        if (!$userId) {
            return false;
        }
        
        return ClienteMembresia::where('cliente_id', $userId)
            ->where('estado', 'activa')
            ->whereHas('membresia', function($query) use ($membresia_requerida) {
                $query->where('id', '>=', $membresia_requerida);
            })
            ->exists();
    }
}