<?php

namespace App\Filament\Usuariocasino\Pages;

use App\Models\BilleteraCliente;
use App\Models\ClienteMembresia;
use App\Models\JuegosOnline;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;


class JuegosCasino extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';
    protected static ?string $title = 'Juegos del Casino';
    protected static ?string $navigationLabel = 'Juegos Disponibles';
    protected static ?string $slug = 'juegos-casino';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.usuariocasino.pages.juegos-casino';

    public $juegos;
    public $membresiaActual = null;
    public $saldoDisponible = 0;

    public function mount(): void
    {
        // Verificar y actualizar membresías vencidas antes de cargar los juegos
        $this->verificarMembresiasVencidas();

        $this->loadJuegos();
        $this->loadUserInfo();
    }

    // Añadir este nuevo método
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

    protected function loadJuegos(): void
    {
        // Cargar todos los juegos activos
        $this->juegos = JuegosOnline::where('estado', 'activo')->get();
    }

    protected function loadUserInfo(): void
    {
        $user = Auth::guard('cliente')->user();

        if (!$user) {
            return;
        }

        // Obtener la membresía del usuario
        $membresia = ClienteMembresia::with('membresia')
            ->where('cliente_id', $user->id)
            ->where('estado', 'activa')
            ->first();

        $this->membresiaActual = $membresia ? $membresia->membresia : null;

        // Obtener el saldo del usuario
        $billetera = BilleteraCliente::where('cliente_id', $user->id)->first();
        $this->saldoDisponible = $billetera ? $billetera->balance_real : 0;
    }

    public function puedeJugar($juegoId): bool
    {
        $juego = JuegosOnline::find($juegoId);

        if (!$juego || $juego->estado !== 'activo') {
            return false;
        }

        // Si el juego no requiere membresía, todos pueden jugar
        if (!$juego->membresia_requerida) {
            return true;
        }

        // Si requiere membresía, verificar si el usuario la tiene
        if (!$this->membresiaActual) {
            return false;
        }

        // Verificar si la membresía del usuario es suficiente
        return $this->membresiaActual->id >= $juego->membresia_requerida;
    }

    public function jugar($juegoId)
    {
        $juego = JuegosOnline::find($juegoId);

        if (!$juego) {
            Notification::make()
                ->title('Juego no encontrado')
                ->danger()
                ->send();

            return;
        }

        if (!$this->puedeJugar($juegoId)) {
            Notification::make()
                ->title('No tienes los requisitos necesarios para este juego')
                ->body('Es posible que necesites una membresía superior')
                ->warning()
                ->send();

            return;
        }

        // Verificar si hay una URL o ruta nombrada en pagina_juego
        if (!empty($juego->pagina_juego)) {
            // Si la página del juego comienza con http, es una URL completa
            if (str_starts_with($juego->pagina_juego, 'http')) {
                return redirect()->away($juego->pagina_juego);
            }

            // Si comienza con /, es una ruta relativa
            if (str_starts_with($juego->pagina_juego, '/')) {
                return redirect($juego->pagina_juego);
            }

            // De lo contrario, intentamos buscarla como una ruta nombrada
            try {
                return redirect()->route($juego->pagina_juego);
            } catch (\Exception $e) {
                // Si la ruta no existe, redirigimos a la página principal de juegos
                Notification::make()
                    ->title('Error al cargar el juego')
                    ->body('La ruta especificada no existe')
                    ->danger()
                    ->send();

                return redirect()->route('filament.usuariocasino.pages.juegos-casino');
            }
        }

        // Si no hay pagina_juego definida, redirigir a la página principal
        return redirect()->route('filament.usuariocasino.pages.juegos-casino');
    }

    public function getJuegosPorCategoria(): array
    {
        $categorias = [];

        foreach ($this->juegos as $juego) {
            $categoria = $juego->categoria->nombre ?? 'General';

            if (!isset($categorias[$categoria])) {
                $categorias[$categoria] = [];
            }

            $categorias[$categoria][] = $juego;
        }

        return $categorias;
    }
}
