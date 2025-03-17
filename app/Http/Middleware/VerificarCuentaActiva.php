<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use App\Models\UserCliente; // Asegúrate de importar el modelo correcto

class VerificarCuentaActiva
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Solo verificar si hay un usuario autenticado
        if (Auth::guard('cliente')->check()) {
            $userId = Auth::guard('cliente')->id();
            
            // Obtener el modelo completo de UserCliente para poder usar save()
            $user = UserCliente::find($userId);
            
            if (!$user) {
                return $next($request);
            }
            
            // Verificar si una cuenta suspendida debe ser reactivada
            if ($user->estado_cuenta === 'suspendida' && 
                $user->fecha_suspension && 
                Carbon::now()->gte($user->fecha_suspension)) {
                
                // Actualizar automáticamente a estado activo
                $user->estado_cuenta = 'activa';
                $user->fecha_suspension = null;
                $user->save();
                
                // Notificar al usuario que su cuenta ha sido reactivada
                Notification::make()
                    ->title('Cuenta reactivada')
                    ->body('Tu cuenta ha sido reactivada automáticamente.')
                    ->success()
                    ->send();
                
                // Continuar normalmente ya que la cuenta ahora está activa
                return $next($request);
            }
            
            // Verificar si la cuenta está bloqueada
            if ($user->estado_cuenta === 'bloqueada' || $user->estado_cuenta === 'suspendida') {
                // Cerrar sesión
                Auth::guard('cliente')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Mensaje personalizado según el tipo de restricción
                $mensaje = ($user->estado_cuenta === 'bloqueada') 
                    ? 'Tu cuenta ha sido bloqueada permanentemente.' 
                    : 'Tu cuenta está suspendida hasta el ' . $user->fecha_suspension->format('d/m/Y H:i:s');
                
                // Mostrar notificación y redirigir
                Notification::make()
                    ->title('Acceso restringido')
                    ->body($mensaje . ' Para asistencia, contacta con atención al cliente.')
                    ->danger()
                    ->persistent()
                    ->send();
                
                return redirect()->route('filament.usuariocasino.auth.login');
            }
        }
        
        return $next($request);
    }
}