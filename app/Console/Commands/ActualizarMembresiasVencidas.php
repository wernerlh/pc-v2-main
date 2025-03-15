<?php

// 1. Crea un comando de artisan: app/Console/Commands/ActualizarMembresiasVencidas.php
namespace App\Console\Commands;

use App\Models\ClienteMembresia;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ActualizarMembresiasVencidas extends Command
{
    protected $signature = 'membresias:verificar-vencidas';
    protected $description = 'Actualiza el estado de las membresías que ya han expirado';

    public function handle()
    {
        $this->info('Iniciando verificación de membresías vencidas...');
        
        // Obtener todas las membresías activas que ya han expirado
        $membresiasVencidas = ClienteMembresia::where('estado', 'activa')
            ->where('fecha_vencimiento', '<', Carbon::now())
            ->get();
            
        $contador = 0;
        
        foreach ($membresiasVencidas as $membresia) {
            $membresia->estado = 'vencida';
            $membresia->save();
            $contador++;
        }
        
        $this->info("Proceso completado. Se han actualizado {$contador} membresías a estado 'vencida'.");
        
        return 0;
    }
}