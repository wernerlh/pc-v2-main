<x-filament-panels::page>
<style>
    .opciones-container {
        display: flex;
        justify-content: center;
        gap: 1.5rem;
    }

    .opcion-item {
        flex: 1;
        max-width: 140px;
        transition: all 0.3s ease;
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
        border-radius: 0.5rem;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
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

    .resultado-area {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-top: 2rem;
        margin-bottom: 2rem;
    }

    .resultado-item {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .icono-juego {
        font-size: 4rem;
        margin-top: 1rem;
    }

    .icono-maquina {
        animation: girar-opcion 1s forwards;
    }

    .vs-badge {
        background-color: #f43f5e;
        color: white;
        font-weight: bold;
        padding: 0.5rem 1rem;
        border-radius: 1rem;
        align-self: center;
        margin-top: 2.5rem;
    }

    @keyframes girar-opcion {
        0% { transform: rotateY(0); opacity: 0; }
        100% { transform: rotateY(720deg); opacity: 1; }
    }
</style>

<div class="space-y-6">
    <div class="p-6 bg-primary-500/10 rounded-xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-blue-500/10 rounded-lg">
            <div>
                <h2 class="text-xl font-bold mb-2">Juego de Piedra, Papel o Tijera</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    Elige tu jugada, apuesta y gana.
                </p>
            </div>
            <div>
                <h3 class="text-lg font-medium">Saldo disponible:</h3>
                <p class="text-2xl font-bold">S/ {{ number_format($saldoDisponible, 2) }}</p>
            </div>
        </div>

        @if($resultado)
            <div class="resultado-area">
                <div class="resultado-item">
                    <span class="font-medium text-lg">Tu elección:</span>
                    <div class="icono-juego">
                        @if($data['eleccion'] == 'piedra')
                            ✊
                        @elseif($data['eleccion'] == 'papel')
                            ✋
                        @elseif($data['eleccion'] == 'tijera')
                            ✌️
                        @endif
                    </div>
                </div>
                
                <div class="vs-badge">VS</div>
                
                <div class="resultado-item">
                    <span class="font-medium text-lg">La máquina eligió:</span>
                    <div class="icono-juego icono-maquina">
                        @if($eleccionMaquina == 'piedra')
                            ✊
                        @elseif($eleccionMaquina == 'papel')
                            ✋
                        @elseif($eleccionMaquina == 'tijera')
                            ✌️
                        @endif
                    </div>
                </div>
            </div>

            <div class="text-center mb-6 p-4 rounded-lg {{ $gano ? 'bg-success-500/20' : ($eleccionMaquina == $data['eleccion'] ? 'bg-warning-500/20' : 'bg-danger-500/20') }}">
                <p class="text-xl font-bold">{{ $mensaje }}</p>
                @if($gano)
                    <p class="text-lg mt-2">¡Has ganado S/ {{ number_format($montoGanado, 2) }}!</p>
                @elseif($eleccionMaquina == $data['eleccion'])
                    <p class="text-lg mt-2">Empate. Tu apuesta ha sido devuelta.</p>
                @else
                    <p class="text-lg mt-2">Inténtalo nuevamente</p>
                @endif
            </div>
        @endif

        <form wire:submit.prevent="jugar" id="formJuego" class="mt-6">
            <div class="mb-6">
                <label class="block mb-3 font-medium text-lg text-center">Elige tu jugada:</label>
                <div class="opciones-container">
                    <label class="opcion-item cursor-pointer" id="label-piedra">
                        <input type="radio" name="data.eleccion" value="piedra" wire:model="data.eleccion" class="radio-opcion">
                        <div class="radio-contenedor border-primary-500 bg-primary-500/10 hover:bg-primary-500/20 text-center" id="div-piedra">
                            <div class="text-3xl mb-2">✊</div>
                            <span class="font-bold">PIEDRA</span>
                        </div>
                    </label>
                    <label class="opcion-item cursor-pointer" id="label-papel">
                        <input type="radio" name="data.eleccion" value="papel" wire:model="data.eleccion" class="radio-opcion">
                        <div class="radio-contenedor border-primary-500 bg-primary-500/10 hover:bg-primary-500/20 text-center" id="div-papel">
                            <div class="text-3xl mb-2">✋</div>
                            <span class="font-bold">PAPEL</span>
                        </div>
                    </label>
                    <label class="opcion-item cursor-pointer" id="label-tijera">
                        <input type="radio" name="data.eleccion" value="tijera" wire:model="data.eleccion" class="radio-opcion">
                        <div class="radio-contenedor border-primary-500 bg-primary-500/10 hover:bg-primary-500/20 text-center" id="div-tijera">
                            <div class="text-3xl mb-2">✌️</div>
                            <span class="font-bold">TIJERA</span>
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
                    id="btnJugar"
                    class="w-full py-3">
                    {{ $saldoDisponible <= 0 ? 'Sin saldo suficiente' : '¡JUGAR!' }}
                </x-filament::button>
            </div>
        </form>
    </div>
    
    <div class="p-6 bg-gray-100 dark:bg-gray-800 rounded-xl">
        <h2 class="text-xl font-bold mb-4">Reglas del Juego</h2>
        <ul class="list-disc list-inside space-y-2">
            <li>La piedra vence a las tijeras (las rompe)</li>
            <li>Las tijeras vencen al papel (lo cortan)</li>
            <li>El papel vence a la piedra (la envuelve)</li>
            <li>Si ambos eligen lo mismo, es un empate y recuperas tu apuesta</li>
            <li>Si ganas, recibirás el doble de lo apostado</li>
        </ul>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', function () {
        const divPiedra = document.getElementById('div-piedra');
        const divPapel = document.getElementById('div-papel');
        const divTijera = document.getElementById('div-tijera');
        const labelPiedra = document.getElementById('label-piedra');
        const labelPapel = document.getElementById('label-papel');
        const labelTijera = document.getElementById('label-tijera');
        
        // Función para actualizar selección visualmente
        function actualizarSeleccion() {
            const eleccionPiedra = document.querySelector('input[name="data.eleccion"][value="piedra"]');
            const eleccionPapel = document.querySelector('input[name="data.eleccion"][value="papel"]');
            const eleccionTijera = document.querySelector('input[name="data.eleccion"][value="tijera"]');
            
            if (eleccionPiedra && eleccionPiedra.checked) {
                divPiedra.classList.add('bg-primary-500', 'border-white', 'opcion-seleccionada');
                divPiedra.classList.remove('bg-primary-500/10');
                divPapel.classList.remove('bg-primary-500', 'border-white', 'opcion-seleccionada');
                divPapel.classList.add('bg-primary-500/10');
                divTijera.classList.remove('bg-primary-500', 'border-white', 'opcion-seleccionada');
                divTijera.classList.add('bg-primary-500/10');
            } else if (eleccionPapel && eleccionPapel.checked) {
                divPapel.classList.add('bg-primary-500', 'border-white', 'opcion-seleccionada');
                divPapel.classList.remove('bg-primary-500/10');
                divPiedra.classList.remove('bg-primary-500', 'border-white', 'opcion-seleccionada');
                divPiedra.classList.add('bg-primary-500/10');
                divTijera.classList.remove('bg-primary-500', 'border-white', 'opcion-seleccionada');
                divTijera.classList.add('bg-primary-500/10');
            } else if (eleccionTijera && eleccionTijera.checked) {
                divTijera.classList.add('bg-primary-500', 'border-white', 'opcion-seleccionada');
                divTijera.classList.remove('bg-primary-500/10');
                divPiedra.classList.remove('bg-primary-500', 'border-white', 'opcion-seleccionada');
                divPiedra.classList.add('bg-primary-500/10');
                divPapel.classList.remove('bg-primary-500', 'border-white', 'opcion-seleccionada');
                divPapel.classList.add('bg-primary-500/10');
            }
        }

        // Eventos para actualizar selección
        labelPiedra.addEventListener('click', function() {
            setTimeout(actualizarSeleccion, 10);
        });
        
        labelPapel.addEventListener('click', function() {
            setTimeout(actualizarSeleccion, 10);
        });
        
        labelTijera.addEventListener('click', function() {
            setTimeout(actualizarSeleccion, 10);
        });
        
        // Verificar selección inicial
        actualizarSeleccion();
        
        // Manejar eventos de Livewire
        Livewire.hook('message.processed', () => {
            actualizarSeleccion();
        });
    });
</script>
</x-filament-panels::page>