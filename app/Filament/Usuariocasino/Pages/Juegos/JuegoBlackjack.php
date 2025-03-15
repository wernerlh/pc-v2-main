<?php
// filepath: d:\CLASES\IDAT\CICLO 2\PDCCV\EXAMEN\pc-v2-main\pc-v2-main\app\Filament\Usuariocasino\Pages\Juegos\JuegoBlackjack.php

namespace App\Filament\Usuariocasino\Pages\Juegos;

use App\Models\ClienteMembresia;
use App\Traits\VerificaMembresiasTrait;
use App\Models\BilleteraCliente;
use App\Models\JuegosOnline;
use App\Models\TransaccionesJuego;
use App\Services\BilleteraService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class JuegoBlackjack extends Page
{
    use InteractsWithForms;
    use VerificaMembresiasTrait;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $title = 'Blackjack / 21';
    protected static ?string $navigationLabel = 'Jugar Blackjack';
    protected static ?string $slug = 'juego-blackjack';
    protected static ?string $navigationGroup = 'Juegos';
    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.usuariocasino.pages.juegos.juego-blackjack';

    // Variables del juego
    public $saldoDisponible = 0;
    public $apuesta = 1;
    public $juegoIniciado = false;
    public $juegoTerminado = false;
    public $cartasJugador = [];
    public $cartasCrupier = [];
    public $puntosJugador = 0;
    public $puntosCrupier = 0;
    public $resultado = null;
    public $mensaje = '';
    public $montoGanado = 0;
    public $baraja = [];

    // Constructor para inyectar el servicio de billetera
    protected $billeteraService;

    public function __construct()
    {
        $this->billeteraService = app(BilleteraService::class);
    }

    public function mount(): void
    {
        // Verificar y actualizar membresías vencidas
        $this->verificarMembresiasVencidas();

        // Verificar la membresía del usuario
        $user = Auth::guard('cliente')->user();

        // Obtener el juego y verificar la membresía requerida
        $juego = JuegosOnline::where('nombre', 'Blackjack')->first();

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
        
        // Inicializar la baraja
        $this->inicializarBaraja();
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

    protected function inicializarBaraja()
    {
        $palos = ['corazones', 'diamantes', 'picas', 'treboles'];
        $valores = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];
        
        $baraja = [];
        
        foreach ($palos as $palo) {
            foreach ($valores as $valor) {
                // Determinar el valor numérico de la carta
                $valorNumerico = 0;
                
                if ($valor == 'A') {
                    $valorNumerico = 11; // El As inicialmente vale 11
                } elseif (in_array($valor, ['J', 'Q', 'K'])) {
                    $valorNumerico = 10;
                } else {
                    $valorNumerico = (int)$valor;
                }
                
                $baraja[] = [
                    'palo' => $palo,
                    'valor' => $valor,
                    'valor_numerico' => $valorNumerico
                ];
            }
        }
        
        // Mezclar la baraja
        shuffle($baraja);
        
        $this->baraja = $baraja;
    }

    public function iniciarJuego()
    {
        // Validar la apuesta
        if ($this->apuesta < 1) {
            Notification::make()
                ->title('Error')
                ->body('La apuesta mínima es S/ 1.00')
                ->danger()
                ->send();
            return;
        }

        if ($this->apuesta > $this->saldoDisponible) {
            Notification::make()
                ->title('Saldo insuficiente')
                ->body('No tienes suficiente saldo para realizar esta apuesta')
                ->danger()
                ->send();
            return;
        }

        // Cobrar la apuesta
        $user = Auth::guard('cliente')->user();
        $billetera = BilleteraCliente::where('cliente_id', $user->id)->first();

        if ($billetera->balance_real < $this->apuesta) {
            Notification::make()
                ->title('Saldo insuficiente')
                ->body('No tienes suficiente saldo para realizar esta apuesta.')
                ->danger()
                ->send();
            return;
        }

        // Registrar la apuesta
        $this->billeteraService->registrarApuesta($user->id, $this->apuesta);
        
        // Actualizar el saldo
        $this->cargarSaldo();

        // Reiniciar el juego
        $this->juegoIniciado = true;
        $this->juegoTerminado = false;
        $this->resultado = null;
        $this->mensaje = '';
        $this->montoGanado = 0;
        $this->cartasJugador = [];
        $this->cartasCrupier = [];
        $this->puntosJugador = 0;
        $this->puntosCrupier = 0;

        // Inicializar la baraja
        $this->inicializarBaraja();

        // Repartir las cartas iniciales
        $this->cartasJugador[] = array_pop($this->baraja);
        $this->cartasCrupier[] = array_pop($this->baraja);
        $this->cartasJugador[] = array_pop($this->baraja);
        $this->cartasCrupier[] = array_pop($this->baraja);

        // Calcular puntos iniciales
        $this->calcularPuntosJugador();
        $this->calcularPuntosCrupier();

        // Verificar si hay Blackjack
        if ($this->puntosJugador == 21) {
            $this->plantarse();
        }
    }

    protected function calcularPuntosJugador()
    {
        $puntos = 0;
        $ases = 0;

        foreach ($this->cartasJugador as $carta) {
            $puntos += $carta['valor_numerico'];
            if ($carta['valor'] == 'A') {
                $ases++;
            }
        }

        // Ajustar valor de los ases si es necesario
        while ($puntos > 21 && $ases > 0) {
            $puntos -= 10; // Convertir un As de 11 a 1
            $ases--;
        }

        $this->puntosJugador = $puntos;
    }

    protected function calcularPuntosCrupier()
    {
        $puntos = 0;
        $ases = 0;

        foreach ($this->cartasCrupier as $carta) {
            $puntos += $carta['valor_numerico'];
            if ($carta['valor'] == 'A') {
                $ases++;
            }
        }

        // Ajustar valor de los ases si es necesario
        while ($puntos > 21 && $ases > 0) {
            $puntos -= 10; // Convertir un As de 11 a 1
            $ases--;
        }

        $this->puntosCrupier = $puntos;
    }

    public function pedirCarta()
    {
        if (!$this->juegoIniciado || $this->juegoTerminado) {
            return;
        }
        
        // Repartir una carta al jugador
        $this->cartasJugador[] = array_pop($this->baraja);
        
        // Calcular puntos
        $this->calcularPuntosJugador();
        
        // Verificar si el jugador se pasó
        if ($this->puntosJugador > 21) {
            $this->finalizarJuego('derrota', 'Te has pasado de 21. ¡Has perdido!');
        }
    }

    public function plantarse()
    {
        if (!$this->juegoIniciado || $this->juegoTerminado) {
            return;
        }
        
        // Jugar el turno del crupier
        $this->jugarTurnoCrupier();
        
        // Determinar el ganador
        $this->determinarGanador();
    }

    protected function jugarTurnoCrupier()
    {
        // El crupier sigue pidiendo cartas hasta tener 17 o más
        while ($this->puntosCrupier < 17) {
            $this->cartasCrupier[] = array_pop($this->baraja);
            $this->calcularPuntosCrupier();
        }
    }

    protected function determinarGanador()
    {
        $user = Auth::guard('cliente')->user();
        
        // Si el jugador tiene Blackjack (21 con solo 2 cartas)
        $blackjackJugador = $this->puntosJugador == 21 && count($this->cartasJugador) == 2;
        $blackjackCrupier = $this->puntosCrupier == 21 && count($this->cartasCrupier) == 2;
        
        if ($blackjackJugador && !$blackjackCrupier) {
            // Blackjack paga 3:2
            $ganancia = $this->apuesta * 2.5;
            $this->montoGanado = $ganancia;
            $this->billeteraService->registrarGanancia($user->id, $ganancia);
            $this->finalizarJuego('blackjack', '¡Blackjack! Has ganado con 21 puntos.');
        } 
        elseif ($this->puntosCrupier > 21) {
            // El crupier se pasó
            $ganancia = $this->apuesta * 2;
            $this->montoGanado = $ganancia;
            $this->billeteraService->registrarGanancia($user->id, $ganancia);
            $this->finalizarJuego('victoria', '¡El crupier se pasó de 21! Has ganado.');
        } 
        elseif ($this->puntosJugador > $this->puntosCrupier) {
            // El jugador tiene más puntos que el crupier
            $ganancia = $this->apuesta * 2;
            $this->montoGanado = $ganancia;
            $this->billeteraService->registrarGanancia($user->id, $ganancia);
            $this->finalizarJuego('victoria', '¡Has ganado con ' . $this->puntosJugador . ' puntos contra ' . $this->puntosCrupier . '!');
        }
        elseif ($this->puntosJugador == $this->puntosCrupier) {
            // Empate
            $this->billeteraService->registrarGanancia($user->id, $this->apuesta);
            $this->finalizarJuego('empate', 'Empate a ' . $this->puntosJugador . ' puntos. Tu apuesta ha sido devuelta.');
        }
        else {
            // El crupier tiene más puntos
            $this->finalizarJuego('derrota', 'Has perdido. El crupier tiene ' . $this->puntosCrupier . ' puntos contra tus ' . $this->puntosJugador . '.');
        }
        
        // Registrar la transacción del juego
        $this->registrarTransaccion();
    }

    protected function finalizarJuego($resultado, $mensaje)
    {
        $this->juegoTerminado = true;
        $this->resultado = $resultado;
        $this->mensaje = $mensaje;
        
        // Actualizar saldo
        $this->cargarSaldo();
        
        // Mostrar notificación
        $color = 'danger';
        if ($resultado == 'victoria' || $resultado == 'blackjack') {
            $color = 'success';
        } elseif ($resultado == 'empate') {
            $color = 'warning';
        }
        
        Notification::make()
            ->title($mensaje)
            ->color($color)
            ->send();
    }
    
    protected function registrarTransaccion()
    {
        $user = Auth::guard('cliente')->user();
        $billetera = BilleteraCliente::where('cliente_id', $user->id)->first();
        
        // Buscar o crear el juego
        $juego = JuegosOnline::where('nombre', 'Blackjack')->first();
        if (!$juego) {
            $juego = new JuegosOnline();
            $juego->nombre = 'Blackjack';
            $juego->descripcion = 'El clásico juego de cartas 21';
            $juego->categoria_id = 1;
            $juego->estado = 'activo';
            $juego->pagina_juego = 'filament.usuariocasino.pages.juego-blackjack';
            $juego->save();
        }
        
        // Registrar la transacción
        $transaccion = new TransaccionesJuego();
        $transaccion->cliente_id = $user->id;
        $transaccion->juego_id = $juego->id;
        $transaccion->fecha_hora = now();
        $transaccion->monto_apostado = $this->apuesta;
        $transaccion->monto_ganado = $this->resultado == 'derrota' ? 0 : ($this->resultado == 'empate' ? $this->apuesta : $this->montoGanado);
        $transaccion->tipo_transaccion = $this->resultado == 'derrota' ? 'apuesta' : 'ganancia';
        $transaccion->balance_anterior = $billetera->getOriginal('balance_real');
        $transaccion->balance_posterior = $billetera->balance_real;
        $transaccion->detalles_juego = json_encode([
            'puntosJugador' => $this->puntosJugador,
            'puntosCrupier' => $this->puntosCrupier,
            'cartasJugador' => $this->cartasJugador,
            'cartasCrupier' => $this->cartasCrupier,
            'resultado' => $this->resultado
        ]);
        $transaccion->save();
    }

    public function nuevaPartida()
    {
        $this->juegoIniciado = false;
        $this->juegoTerminado = false;
        $this->resultado = null;
        $this->mensaje = '';
        $this->montoGanado = 0;
        $this->cartasJugador = [];
        $this->cartasCrupier = [];
        $this->puntosJugador = 0;
        $this->puntosCrupier = 0;
    }
}