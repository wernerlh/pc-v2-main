<x-filament-panels::page>
    <style>
        /* Estilos para las cartas */
        .mesa-juego {
            background-color: #2d6a4f;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: inset 0 0 50px rgba(0, 0, 0, 0.5);
        }

        .area-jugador,
        .area-crupier {
            margin-bottom: 1.5rem;
        }

        .cartas-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
            min-height: 120px;
        }

        .carta {
            position: relative;
            width: 80px;
            height: 120px;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 0.5rem;
            transform-style: preserve-3d;
            transition: transform 0.5s;
        }

        .carta.oculta {
            background-image: linear-gradient(45deg, #1e40af 25%, #3b82f6 25%, #3b82f6 50%, #1e40af 50%, #1e40af 75%, #3b82f6 75%, #3b82f6 100%);
            background-size: 20px 20px;
        }

        .carta-nueva {
            animation: carta-entrada 0.5s forwards;
        }

        .carta-valor {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .carta-superior {
            display: flex;
            align-items: flex-start;
        }

        .carta-inferior {
            display: flex;
            align-items: flex-end;
            justify-content: flex-end;
            transform: rotate(180deg);
        }

        .carta-palo {
            font-size: 1.2rem;
        }

        .carta-centro {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2rem;
        }

        .palo-rojo {
            color: #ef4444;
        }

        .palo-negro {
            color: #000000;
        }

        /* Animaciones */
        @keyframes carta-entrada {
            from {
                opacity: 0;
                transform: translateY(-20px) rotateY(90deg);
            }

            to {
                opacity: 1;
                transform: translateY(0) rotateY(0);
            }
        }

        /* Estilos para botones de acción */
        .botones-accion {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 1rem 0;
        }

        .btn-accion {
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: all 0.2s;
            min-width: 120px;
        }

        .btn-pedir {
            background-color: #10b981;
            color: white;
        }

        .btn-pedir:hover {
            background-color: #059669;
        }

        .btn-plantarse {
            background-color: #f43f5e;
            color: white;
        }

        .btn-plantarse:hover {
            background-color: #e11d48;
        }

        /* Estilos para apuestas */
        .apuesta-input {
            border: 2px solid #3b82f6;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 1.1rem;
            font-weight: 500;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            background-color: white;
            color: black;
        }

        .dark .apuesta-input {
            background-color: #1e293b;
            color: white;
            border-color: #4b5563;
        }

        .apuesta-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
        }

        /* Estilos para puntuaciones */
        .puntuacion {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            font-weight: 600;
            display: inline-block;
            margin-left: 1rem;
        }

        /* Indicadores de estado */
        .estado-juego {
            text-align: center;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .estado-victoria {
            background-color: rgba(16, 185, 129, 0.2);
            color: #065f46;
        }

        .dark .estado-victoria {
            color: #10b981;
        }

        .estado-derrota {
            background-color: rgba(239, 68, 68, 0.2);
            color: #991b1b;
        }

        .dark .estado-derrota {
            color: #f87171;
        }

        .estado-empate {
            background-color: rgba(245, 158, 11, 0.2);
            color: #92400e;
        }

        .dark .estado-empate {
            color: #fbbf24;
        }
    </style>

    <div class="space-y-6">
        <div class="p-6 bg-primary-500/10 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-blue-500/10 rounded-lg">
                <div>
                    <h2 class="text-xl font-bold mb-2">Blackjack / 21</h2>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        Acércate lo más posible a 21 sin pasarte.
                    </p>
                </div>
                <div class="bg-primary-600 p-6 rounded-lg shadow-lg text-white">
                    <h3 class="text-lg font-medium">Saldo disponible:</h3>
                    <p class="text-2xl font-bold">S/ {{ number_format($saldoDisponible, 2) }}</p>
                </div>
            </div>

            <!-- Área de juego -->
            <div class="mesa-juego">
                @if($juegoIniciado)
                <!-- Área del crupier -->
                <div class="area-crupier">
                    <h3 class="text-white font-bold text-lg">Crupier
                        @if($juegoTerminado)
                        <span class="puntuacion">{{ $puntosCrupier }} puntos</span>
                        @endif
                    </h3>
                    <div class="cartas-container">
                        @foreach($cartasCrupier as $index => $carta)
                        <div class="carta {{ $index === 1 && !$juegoTerminado ? 'oculta' : '' }} {{ $index >= count($cartasCrupier) - 1 && !$juegoTerminado ? 'carta-nueva' : '' }}">
                            @if(!($index === 1 && !$juegoTerminado))
                            <div class="carta-superior">
                                <div class="carta-valor {{ in_array($carta['palo'], ['corazones', 'diamantes']) ? 'palo-rojo' : 'palo-negro' }}">
                                    {{ $carta['valor'] }}
                                </div>
                            </div>
                            <div class="carta-centro {{ in_array($carta['palo'], ['corazones', 'diamantes']) ? 'palo-rojo' : 'palo-negro' }}">
                                @if($carta['palo'] == 'corazones') ♥
                                @elseif($carta['palo'] == 'diamantes') ♦
                                @elseif($carta['palo'] == 'picas') ♠
                                @elseif($carta['palo'] == 'treboles') ♣
                                @endif
                            </div>
                            <div class="carta-inferior">
                                <div class="carta-valor {{ in_array($carta['palo'], ['corazones', 'diamantes']) ? 'palo-rojo' : 'palo-negro' }}">
                                    {{ $carta['valor'] }}
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Área del jugador -->
                <div class="area-jugador">
                    <h3 class="text-white font-bold text-lg">Tu mano <span class="puntuacion">{{ $puntosJugador }} puntos</span></h3>
                    <div class="cartas-container">
                        @foreach($cartasJugador as $index => $carta)
                        <div class="carta {{ count($cartasJugador) > 2 && $index >= count($cartasJugador) - 1 ? 'carta-nueva' : '' }}">
                            <div class="carta-superior">
                                <div class="carta-valor {{ in_array($carta['palo'], ['corazones', 'diamantes']) ? 'palo-rojo' : 'palo-negro' }}">
                                    {{ $carta['valor'] }}
                                </div>
                            </div>
                            <div class="carta-centro {{ in_array($carta['palo'], ['corazones', 'diamantes']) ? 'palo-rojo' : 'palo-negro' }}">
                                @if($carta['palo'] == 'corazones') ♥
                                @elseif($carta['palo'] == 'diamantes') ♦
                                @elseif($carta['palo'] == 'picas') ♠
                                @elseif($carta['palo'] == 'treboles') ♣
                                @endif
                            </div>
                            <div class="carta-inferior">
                                <div class="carta-valor {{ in_array($carta['palo'], ['corazones', 'diamantes']) ? 'palo-rojo' : 'palo-negro' }}">
                                    {{ $carta['valor'] }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Botones de acción -->
                @if(!$juegoTerminado)
                <div class="botones-accion">
                    <button class="btn-accion btn-pedir" wire:click="pedirCarta">Pedir carta</button>
                    <button class="btn-accion btn-plantarse" wire:click="plantarse">Plantarse</button>
                </div>
                @endif

                <!-- Estado del juego -->
                @if($juegoTerminado)
                <div class="estado-juego {{ $resultado == 'victoria' ? 'estado-victoria' : ($resultado == 'empate' ? 'estado-empate' : 'estado-derrota') }}">
                    @if($resultado == 'victoria')
                    ¡Victoria! Has ganado S/ {{ number_format($montoGanado, 2) }}
                    @elseif($resultado == 'empate')
                    Empate. Tu apuesta ha sido devuelta.
                    @elseif($resultado == 'blackjack')
                    ¡Blackjack! Has ganado S/ {{ number_format($montoGanado, 2) }}
                    @else
                    Derrota. Has perdido tu apuesta.
                    @endif
                </div>
                <div class="text-center mt-4">
                    <x-filament::button
                        wire:click="nuevaPartida"
                        color="success"
                        size="lg">
                        Nueva partida
                    </x-filament::button>
                </div>
                @endif
                @else
                <!-- Formulario de apuesta inicial -->
                <div class="p-6 bg-white/10 rounded-lg">
                    <h3 class="text-center text-white font-bold text-lg mb-4">Coloca tu apuesta para comenzar</h3>
                    <div class="mb-6">
                        <label for="apuesta" class="block text-lg font-bold mb-2 text-center text-white">Tu apuesta (S/):</label>
                        <div class="flex justify-center">
                            <div class="w-full max-w-xs">
                                <input type="number" wire:model="apuesta" id="apuesta" min="1" max="{{ $saldoDisponible }}" step="1"
                                    class="apuesta-input w-full text-center" required>
                            </div>
                        </div>
                        <p class="text-sm text-white/80 mt-1 text-center">
                            Mín: S/ 1.00 - Máx: S/ {{ number_format($saldoDisponible, 2) }}
                        </p>
                    </div>
                    <div class="text-center mt-6">
                        <x-filament::button
                            wire:click="iniciarJuego"
                            size="lg"
                            :disabled="$saldoDisponible <= 0"
                            id="btnIniciar"
                            color="primary"
                            class="px-8">
                            {{ $saldoDisponible <= 0 ? 'Sin saldo suficiente' : 'Comenzar partida' }}
                        </x-filament::button>
                    </div>
                </div>
                @endif
            </div>

            @if($mensaje)
            <div class="text-center p-4 rounded-lg {{ $resultado == 'victoria' || $resultado == 'blackjack' ? 'bg-success-500/20' : ($resultado == 'empate' ? 'bg-warning-500/20' : 'bg-danger-500/20') }}">
                <p class="text-xl font-bold">{{ $mensaje }}</p>
            </div>
            @endif
        </div>

        <div class="p-6 bg-gray-100 dark:bg-gray-800 rounded-xl">
            <h2 class="text-xl font-bold mb-4">Reglas del Blackjack</h2>
            <ul class="list-disc list-inside space-y-2">
                <li>El objetivo es conseguir una mano con valor más cercano a 21 que el crupier sin pasarse.</li>
                <li>Las cartas numéricas valen su número, las figuras (J, Q, K) valen 10 y el As vale 1 u 11.</li>
                <li>Si te pasas de 21, pierdes automáticamente.</li>
                <li>El crupier debe pedir carta hasta tener 17 o más puntos.</li>
                <li>Un Blackjack (As + carta de valor 10) paga 3:2.</li>
                <li>Ganas el doble de tu apuesta si vences al crupier.</li>
                <li>Si empatas con el crupier, recuperas tu apuesta.</li>
            </ul>
        </div>
    </div>
</x-filament-panels::page>