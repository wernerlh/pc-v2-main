<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego de Cara y Sello</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
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
        .moneda-cara, .moneda-sello {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            backface-visibility: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
        }
        .moneda-cara {
            background-color: gold;
            color: black;
        }
        .moneda-sello {
            background-color: silver;
            color: black;
            transform: rotateY(180deg);
        }
        @keyframes girar-moneda {
            0% { transform: rotateY(0); }
            100% { transform: rotateY(720deg); }
        }
        
        /* Nuevos estilos para los botones de selección */
        .opcion-seleccionada {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.7);
        }
        
        .opcion-btn {
            transition: all 0.3s ease;
        }
        
        .opcion-btn:hover {
            transform: translateY(-5px);
        }
        
        .icono-opcion {
            font-size: 32px;
            margin-bottom: 8px;
            display: block;
        }
    </style>
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('filament.usuariocasino.pages.juegos-casino') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                ← Volver al Juegos
            </a>
            <div class="text-xl">
                Bienvenido, <span class="font-bold">{{ $user->name }}</span>
            </div>
        </div>

        <div class="bg-gray-800 rounded-lg shadow-lg p-6 max-w-md mx-auto">
            <h1 class="text-3xl font-bold text-center mb-6">Cara y Sello</h1>

            <div class="bg-blue-900 rounded-lg p-4 mb-6 text-center">
                <h2 class="text-xl font-semibold mb-2">Tu Saldo Actual</h2>
                <p class="text-3xl font-bold text-yellow-400">S/ {{ number_format($saldoDisponible, 2) }}</p>
            </div>

            @if(session('error'))
                <div class="bg-red-600 text-white p-4 rounded-lg mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('mensaje'))
                <div class="relative my-6">
                    <div class="moneda {{ session('resultado') ? 'girar' : '' }}" id="moneda">
                        <div class="moneda-cara">CARA</div>
                        <div class="moneda-sello">SELLO</div>
                    </div>
                </div>

                <div class="text-center mb-6 {{ session('gano') ? 'bg-green-600' : 'bg-red-600' }} p-4 rounded-lg">
                    <p class="text-xl font-bold">{{ session('mensaje') }}</p>
                    @if(session('gano'))
                        <p class="text-lg mt-2">¡Tu saldo ha aumentado!</p>
                    @else
                        <p class="text-lg mt-2">Inténtalo nuevamente</p>
                    @endif
                </div>
            @endif

            <form action="{{ route('carasello.jugar') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block mb-2 font-medium">Elige tu apuesta:</label>
                    <div class="flex gap-4">
                        <label class="flex-1 opcion-btn" id="opcion-cara">
                            <input type="radio" name="eleccion" value="cara" class="hidden" required 
                                {{ old('eleccion') == 'cara' ? 'checked' : '' }}>
                            <div class="border-2 border-blue-500 bg-gray-700 rounded-lg p-4 text-center cursor-pointer transition h-full flex flex-col items-center justify-center" id="div-cara">
     
                                <span class="font-bold text-lg">CARA</span>
                            </div>
                        </label>
                        <label class="flex-1 opcion-btn" id="opcion-sello">
                            <input type="radio" name="eleccion" value="sello" class="hidden"
                                {{ old('eleccion') == 'sello' ? 'checked' : '' }}>
                            <div class="border-2 border-blue-500 bg-gray-700 rounded-lg p-4 text-center cursor-pointer transition h-full flex flex-col items-center justify-center" id="div-sello">

                                <span class="font-bold text-lg">SELLO</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div>
                    <label for="apuesta" class="block mb-2 font-medium">Monto a apostar:</label>
                    <input type="number" name="apuesta" id="apuesta" min="1" step="1" max="{{ $saldoDisponible }}"
                           class="w-full bg-gray-700 border border-gray-600 rounded-lg p-3 text-white"
                           required placeholder="Ingrese monto" value="{{ old('apuesta') }}">
                    <p class="text-sm text-gray-400 mt-1">Monto mínimo: S/ 1.00 - Máximo: S/ {{ number_format($saldoDisponible, 2) }}</p>
                </div>

                <button type="submit" 
                        class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-3 px-4 rounded-lg transition"
                        {{ $saldoDisponible <= 0 ? 'disabled' : '' }}>
                    {{ $saldoDisponible <= 0 ? 'Sin saldo suficiente' : '¡Lanzar la moneda!' }}
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Script para la animación de la moneda
            const resultado = "{{ session('resultado') }}";
            if (resultado) {
                const moneda = document.getElementById('moneda');
                moneda.addEventListener('animationend', function() {
                    if (resultado === 'cara') {
                        moneda.style.transform = 'rotateY(0deg)';
                    } else {
                        moneda.style.transform = 'rotateY(180deg)';
                    }
                });
            }
            
            // Script para los botones de selección
            const opcionCara = document.getElementById('opcion-cara');
            const opcionSello = document.getElementById('opcion-sello');
            const divCara = document.getElementById('div-cara');
            const divSello = document.getElementById('div-sello');
            const inputCara = opcionCara.querySelector('input');
            const inputSello = opcionSello.querySelector('input');
            
            // Función para actualizar la visualización
            function actualizarSeleccion() {
                if (inputCara.checked) {
                    divCara.classList.add('bg-blue-600', 'border-white', 'opcion-seleccionada');
                    divCara.classList.remove('bg-gray-700');
                    divSello.classList.remove('bg-blue-600', 'border-white', 'opcion-seleccionada');
                    divSello.classList.add('bg-gray-700');
                } else if (inputSello.checked) {
                    divSello.classList.add('bg-blue-600', 'border-white', 'opcion-seleccionada');
                    divSello.classList.remove('bg-gray-700');
                    divCara.classList.remove('bg-blue-600', 'border-white', 'opcion-seleccionada');
                    divCara.classList.add('bg-gray-700');
                }
            }
            
            // Verificar si hay una selección guardada
            actualizarSeleccion();
            
            // Eventos para las opciones
            opcionCara.addEventListener('click', function() {
                inputCara.checked = true;
                actualizarSeleccion();
            });
            
            opcionSello.addEventListener('click', function() {
                inputSello.checked = true;
                actualizarSeleccion();
            });
        });
    </script>
</body>
</html>