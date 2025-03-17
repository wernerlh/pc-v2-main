<?php
// filepath: d:\CLASES\IDAT\CICLO 2\PDCCV\EXAMEN\pc-v2-main\pc-v2-main\app\Filament\Usuariocasino\Pages\Juegos\JuegoPiedraPapelTijera.php

namespace App\Filament\Usuariocasino\Pages\Juegos;

use App\Models\ClienteMembresia;
use App\Traits\VerificaMembresiasTrait; // Importar el trait
use App\Models\BilleteraCliente;
use App\Models\JuegosOnline;
use App\Models\TransaccionesJuego;
use App\Services\BilleteraService;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JuegoPiedraPapelTijera extends Page
{
    use InteractsWithForms;
    use VerificaMembresiasTrait; // Usar el trait

    protected static bool $shouldRegisterNavigation = false; // Añadir esta línea

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';
    protected static ?string $title = 'Piedra, Papel o Tijera';
    protected static ?string $navigationLabel = 'Jugar Piedra, Papel o Tijera';
    protected static ?string $slug = 'juego-piedra-papel-tijera';
    protected static ?string $navigationGroup = 'Juegos';
    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.usuariocasino.pages.juegos.juego-piedra-papel-tijera';

    // Variables para el juego
    public $saldoDisponible = 0;
    public $resultado = null;
    public $gano = false;
    public $mensaje = '';
    public $montoGanado = 0;
    public $eleccionMaquina = null;

    // Datos del formulario
    public ?array $data = [];

    // Constructor para inyectar el servicio de billetera
    protected $billeteraService;

    public function __construct()
    {
        $this->billeteraService = app(BilleteraService::class);
    }

    public function mount(): void
    {
        // Verificar y actualizar membresías vencidas antes de cargar los juegos
        $this->verificarMembresiasVencidas();

        // Verificar la membresía del usuario
        $user = Auth::guard('cliente')->user();

        // Obtener el juego y verificar la membresía requerida
        $juego = JuegosOnline::where('nombre', 'Piedra, Papel o Tijera')->first();

        if ($juego && $juego->membresia_requerida) {
            // Verificar si el usuario tiene la membresía requerida
            $tieneMembresiaRequerida = ClienteMembresia::where('cliente_id', $user->id)
                ->where('estado', 'activa')
                ->whereHas('membresia', function ($query) use ($juego) {
                    $query->where('id', '>=', $juego->membresia_requerida);
                })
                ->exists();

            if (!$tieneMembresiaRequerida) {
                // Redireccionar y mostrar notificación
                Notification::make()
                    ->title('Membresía insuficiente')
                    ->body('Necesitas una membresía superior para acceder a este juego')
                    ->warning()
                    ->send();

                redirect()->route('filament.usuariocasino.pages.juegos-casino');
                return;
            }
        }

        // Cargar el saldo del usuario
        $this->cargarSaldo();
    }

    protected function cargarSaldo(): void
    {
        $user = Auth::guard('cliente')->user();

        if (!$user) {
            return;
        }

        // Obtener o crear la billetera del usuario
        $billetera = BilleteraCliente::where('cliente_id', $user->id)->first();

        if (!$billetera) {
            $billetera = new BilleteraCliente();
            $billetera->cliente_id = $user->id;
            $billetera->balance_real = 0;
            $billetera->balance_rechazadas = 0;
            $billetera->balance_pendiente = 0;
            $billetera->total_depositado = 0;
            $billetera->total_retirado = 0;
            $billetera->total_ganado = 0;
            $billetera->total_apostado = 0;
            $billetera->moneda = 'PEN';
            $billetera->save();
        }

        $this->saldoDisponible = $billetera->balance_real;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Radio::make('eleccion')
                    ->label('Elige tu jugada:')
                    ->options([
                        'piedra' => 'Piedra',
                        'papel' => 'Papel',
                        'tijera' => 'Tijera',
                    ])
                    ->inline()
                    ->required(),

                TextInput::make('apuesta')
                    ->label('Monto a apostar:')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue($this->saldoDisponible)
                    ->required()
                    ->helperText('Monto mínimo: S/ 1.00 - Máximo: S/ ' . number_format($this->saldoDisponible, 2)),
            ])
            ->statePath('data');
    }

    public function jugar()
    {
        // Validar el formulario
        $data = $this->form->getState();

        $user = Auth::guard('cliente')->user();
        $billetera = BilleteraCliente::where('cliente_id', $user->id)->first();

        // Verificar saldo suficiente
        if ($billetera->balance_real < $data['apuesta']) {
            Notification::make()
                ->title('Saldo insuficiente')
                ->body('No tienes suficiente saldo para realizar esta apuesta.')
                ->danger()
                ->send();
            return;
        }

        // Elección de la máquina
        $opciones = ['piedra', 'papel', 'tijera'];
        $this->eleccionMaquina = $opciones[array_rand($opciones)];
        $this->resultado = $this->eleccionMaquina;

        // Determinar ganador según reglas de Piedra, Papel o Tijera
        $empate = false;
        
        if ($data['eleccion'] === $this->eleccionMaquina) {
            $empate = true;
            $this->mensaje = "¡Empate! Ambos eligieron {$this->eleccionMaquina}.";
            // En caso de empate, no se pierde ni se gana dinero
        } else {
            // Determinar si el jugador ganó
            $this->gano = 
                ($data['eleccion'] === 'piedra' && $this->eleccionMaquina === 'tijera') ||
                ($data['eleccion'] === 'papel' && $this->eleccionMaquina === 'piedra') ||
                ($data['eleccion'] === 'tijera' && $this->eleccionMaquina === 'papel');

            // Calcular nuevo saldo y registrar la apuesta
            $this->billeteraService->registrarApuesta($user->id, $data['apuesta']);

            if ($this->gano) {
                // Si ganó, registrar la ganancia (doble de lo apostado)
                $this->billeteraService->registrarGanancia($user->id, $data['apuesta'] * 2);
                $this->montoGanado = $data['apuesta'] * 2;
                $this->mensaje = "¡Ganaste! {$data['eleccion']} vence a {$this->eleccionMaquina}.";
            } else {
                $this->mensaje = "Perdiste. {$this->eleccionMaquina} vence a {$data['eleccion']}.";
                $this->montoGanado = 0;
            }
        }

        // Buscar el juego "Piedra, Papel o Tijera" o crear uno nuevo
        $juego = JuegosOnline::where('nombre', 'Piedra, Papel o Tijera')->first();
        if (!$juego) {
            $juego = new JuegosOnline();
            $juego->nombre = 'Piedra, Papel o Tijera';
            $juego->descripcion = 'Juego clásico de piedra, papel o tijera';
            $juego->categoria_id = 1;
            $juego->estado = 'activo';
            $juego->pagina_juego = 'filament.usuariocasino.pages.juego-piedra-papel-tijera';
            $juego->save();
        }

        // No registrar transacción si es empate
        if (!$empate) {
            // Registrar la transacción del juego
            $transaccion = new TransaccionesJuego();
            $transaccion->cliente_id = $user->id;
            $transaccion->juego_id = $juego->id;
            $transaccion->fecha_hora = now();
            $transaccion->monto_apostado = $data['apuesta'];
            $transaccion->monto_ganado = $this->gano ? $data['apuesta'] * 2 : 0;
            $transaccion->tipo_transaccion = $this->gano ? 'ganancia' : 'apuesta';
            $transaccion->balance_anterior = $billetera->getOriginal('balance_real');
            $transaccion->balance_posterior = $billetera->balance_real;
            $transaccion->detalles_juego = json_encode([
                'eleccion' => $data['eleccion'],
                'eleccion_maquina' => $this->eleccionMaquina
            ]);
            $transaccion->save();
        }

        // Actualizar el saldo disponible
        $this->cargarSaldo();

        // Mostrar notificación
        $color = $empate ? 'warning' : ($this->gano ? 'success' : 'danger');
        $titulo = $empate ? '¡Empate!' : ($this->gano ? '¡Has ganado!' : 'Has perdido');
        
        Notification::make()
            ->title($titulo)
            ->body($this->mensaje)
            ->color($color)
            ->send();
    }
}