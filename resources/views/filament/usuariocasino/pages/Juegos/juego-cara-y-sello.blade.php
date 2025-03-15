<x-filament-panels::page>
<style>
        .moneda {
            width: 150px;
            height: 150px;
            margin: 0 auto;
            transition: transform 1s ease-in-out;
            transform-style: preserve-3d;
        }
        .moneda.girar {
            animation: girar-moneda 1s forwards;
        }
        .moneda.espera {
            animation: girar-espera 1.5s infinite linear;
        }
        .moneda-inner {
            position: relative;
            width: 100%;
            height: 100%;
            text-align: center;
            transition: transform 0.8s;
            transform-style: preserve-3d;
        }
        .cara, .sello {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            backface-visibility: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.5rem;
        }
        .cara {
            background-color: gold;
            color: black;
        }
        .sello {
            background-color: silver;
            color: black;
            transform: rotateY(180deg);
        }
        @keyframes girar-moneda {
            0% { transform: rotateY(0); }
            100% { transform: rotateY(720deg); }
        }
        
        @keyframes girar-espera {
            0% { transform: rotateY(0); }
            100% { transform: rotateY(360deg); }
        }
        
        .opcion-seleccionada {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.7);
        }
        
        .radio-opcion {
            display: none;
        }
        
        .radio-contenedor {
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid;
            height: 100%;
        }
        
        .radio-contenedor:hover {
            transform: translateY(-5px);
        }

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

        .opciones-container {
            display: flex;
            justify-content: center;
            gap: 2rem;
        }

        .opcion-item {
            flex: 1;
            max-width: 200px;
        }
</style>
    <div class="space-y-6">
        <div class="p-6 bg-primary-500/10 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-blue-500/10 rounded-lg">
                <div>
                    <h2 class="text-xl font-bold mb-2">Juego de Cara y Sello</h2>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        Elige cara o sello, apuesta y prueba tu suerte.
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-medium">Saldo disponible:</h3>
                    <p class="text-2xl font-bold">S/ {{ number_format($saldoDisponible, 2) }}</p>
                </div>
            </div>

            <div class="relative my-6 flex justify-center">
                <div class="moneda" id="moneda">
                    <div class="moneda-inner" id="moneda-inner" style="{{ $resultado == 'sello' ? 'transform: rotateY(180deg);' : '' }}">
                        <div class="cara">CARA</div>
                        <div class="sello">SELLO</div>
                    </div>
                </div>
            </div>

            @if($resultado)
                <div class="text-center mb-6 p-4 rounded-lg {{ $gano ? 'bg-success-500/20' : 'bg-danger-500/20' }}">
                    <p class="text-xl font-bold">{{ $mensaje }}</p>
                    @if($gano)
                        <p class="text-lg mt-2">¡Has ganado S/ {{ number_format($montoGanado, 2) }}!</p>
                    @else
                        <p class="text-lg mt-2">Inténtalo nuevamente</p>
                    @endif
                </div>
            @endif

        <form wire:submit="jugar" id="formJuego">
            <div class="mb-6">
                <label class="block mb-2 font-medium text-lg text-center">Elige tu apuesta:</label>
                <div class="opciones-container">
                    <label class="opcion-item cursor-pointer" id="label-cara">
                        <input type="radio" name="data.eleccion" value="cara" wire:model="data.eleccion" class="radio-opcion">
                        <div class="radio-contenedor border-primary-500 bg-primary-500/10 hover:bg-primary-500/20 rounded-lg p-4 text-center h-full flex flex-col items-center justify-center" id="div-cara">
                            <span class="font-bold text-lg">CARA</span>
                        </div>
                    </label>
                    <label class="opcion-item cursor-pointer" id="label-sello">
                        <input type="radio" name="data.eleccion" value="sello" wire:model="data.eleccion" class="radio-opcion">
                        <div class="radio-contenedor border-primary-500 bg-primary-500/10 hover:bg-primary-500/20 rounded-lg p-4 text-center h-full flex flex-col items-center justify-center" id="div-sello">
                            <span class="font-bold text-lg">SELLO</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mb-6">
                <label for="data.apuesta" class="block text-lg font-bold mb-2 text-center">Tu apuesta (S/):</label>
                <div class="flex justify-center">
                    <div class="w-full max-w-xs">
                        <input type="number" wire:model="data.apuesta" id="data.apuesta" min="1" max="{{ $saldoDisponible }}" step="1" 
                            class="apuesta-input w-full text-center" required>
                    </div>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 text-center">
                    Mín: S/ 1.00 - Máx: S/ {{ number_format($saldoDisponible, 2) }}
                </p>
            </div>
            
            <div class="mt-6">
                <x-filament::button 
                    type="submit" 
                    size="lg"
                    color="primary"
                    :disabled="$saldoDisponible <= 0"
                    id="btnLanzar"
                    class="w-full py-3">
                    {{ $saldoDisponible <= 0 ? 'Sin saldo suficiente' : '¡LANZAR LA MONEDA!' }}
                </x-filament::button>
            </div>
        </form>
        </div>
        
        <div class="p-6 bg-gray-100 dark:bg-gray-800 rounded-xl">
            <h2 class="text-xl font-bold mb-4">Reglas del Juego</h2>
            <ul class="list-disc list-inside space-y-2">
                <li>Elige entre cara o sello.</li>
                <li>Establece el monto que deseas apostar.</li>
                <li>Si aciertas, ¡ganarás el doble de lo apostado!</li>
                <li>Si pierdes, perderás el monto apostado.</li>
                <li>Recuerda jugar con responsabilidad.</li>
            </ul>
        </div>
    </div>
    <script>
        document.addEventListener('livewire:initialized', function () {
            const divCara = document.getElementById('div-cara');
            const divSello = document.getElementById('div-sello');
            const labelCara = document.getElementById('label-cara');
            const labelSello = document.getElementById('label-sello');
            const moneda = document.getElementById('moneda');
            const formJuego = document.getElementById('formJuego');
            const btnLanzar = document.getElementById('btnLanzar');
            
            // Función para actualizar selección visualmente
            function actualizarSeleccion() {
                const eleccionCara = document.querySelector('input[name="data.eleccion"][value="cara"]');
                const eleccionSello = document.querySelector('input[name="data.eleccion"][value="sello"]');
                
                if (eleccionCara && eleccionCara.checked) {
                    divCara.classList.add('bg-primary-500', 'border-white', 'opcion-seleccionada');
                    divCara.classList.remove('bg-primary-500/10');
                    divSello.classList.remove('bg-primary-500', 'border-white', 'opcion-seleccionada');
                    divSello.classList.add('bg-primary-500/10');
                } else if (eleccionSello && eleccionSello.checked) {
                    divSello.classList.add('bg-primary-500', 'border-white', 'opcion-seleccionada');
                    divSello.classList.remove('bg-primary-500/10');
                    divCara.classList.remove('bg-primary-500', 'border-white', 'opcion-seleccionada');
                    divCara.classList.add('bg-primary-500/10');
                }
            }

            // Eventos para actualizar selección
            labelCara.addEventListener('click', function() {
                setTimeout(actualizarSeleccion, 10);
            });
            
            labelSello.addEventListener('click', function() {
                setTimeout(actualizarSeleccion, 10);
            });
            
            // Verificar selección inicial
            actualizarSeleccion();
            
            // Animación mientras espera resultado
            formJuego.addEventListener('submit', function(event) {
                moneda.classList.remove('girar');
                moneda.classList.add('espera');
                btnLanzar.disabled = true;
            });
            
            // Manejar eventos de Livewire
            Livewire.hook('message.processed', () => {
                actualizarSeleccion();
                
                // Cambiar la animación cuando hay resultado
                if (moneda.classList.contains('espera')) {
                    moneda.classList.remove('espera');
                    moneda.classList.add('girar');
                    setTimeout(() => {
                        btnLanzar.disabled = false;
                    }, 1000);
                }
            });
        });
    </script>
</x-filament-panels::page>