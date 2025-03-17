<?php

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

class JuegoCaraYSello extends Page
{
    use InteractsWithForms;
    use VerificaMembresiasTrait; // Usar el trait

    protected static bool $shouldRegisterNavigation = false; // Añadir esta línea

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $title = 'Cara y Sello';
    protected static ?string $navigationLabel = 'Jugar Cara y Sello';
    protected static ?string $slug = 'juego-cara-y-sello';
    protected static ?string $navigationGroup = 'Juegos';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.usuariocasino.pages.juegos.juego-cara-y-sello';

    // Variables para el juego
    public $saldoDisponible = 0;
    public $resultado = null;
    public $gano = false;
    public $mensaje = '';
    public $montoGanado = 0;

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
        $juego = JuegosOnline::where('nombre', 'Cara y Sello')->first();

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
                    ->label('Elige tu apuesta:')
                    ->options([
                        'cara' => 'Cara',
                        'sello' => 'Sello',
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

        // Lanzamiento de la moneda (50% probabilidad)
        $this->resultado = (rand(0, 1) == 1) ? 'cara' : 'sello';

        // Determinar si el jugador ganó
        $this->gano = ($data['eleccion'] == $this->resultado);

        // Calcular nuevo saldo y registrar la apuesta
        $this->billeteraService->registrarApuesta($user->id, $data['apuesta']);

        if ($this->gano) {
            // Si ganó, registrar la ganancia (doble de lo apostado)
            $this->billeteraService->registrarGanancia($user->id, $data['apuesta'] * 2);
            $this->montoGanado = $data['apuesta'] * 2;
            $this->mensaje = '¡Ganaste! El resultado fue: ' . $this->resultado;
        } else {
            $this->mensaje = 'Perdiste. El resultado fue: ' . $this->resultado;
            $this->montoGanado = 0;
        }

        // Buscar el juego "Cara y Sello" o crear uno nuevo
        $juego = JuegosOnline::where('nombre', 'Cara y Sello')->first();
        if (!$juego) {
            $juego = new JuegosOnline();
            $juego->nombre = 'Cara y Sello';
            $juego->descripcion = 'Juego simple de cara o sello';
            $juego->categoria_id = 1;
            $juego->estado = 'activo';
            $juego->save();
        }

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
            'resultado' => $this->resultado
        ]);
        $transaccion->save();

        // Actualizar el saldo disponible
        $this->cargarSaldo();

        // Mostrar notificación
        Notification::make()
            ->title($this->gano ? '¡Has ganado!' : 'Has perdido')
            ->body($this->mensaje)
            ->color($this->gano ? 'success' : 'danger')
            ->send();
    }
}
