<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Piedra, Papel o Tijera</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <a href="{{ route('filament.usuariocasino.pages.juegos-casino') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                ← Volver a Juegos
            </a>
            <div class="text-xl">
                Bienvenido, <span class="font-bold">{{ $user->name }}</span>
            </div>
        </div>

        <div class="bg-gray-800 rounded-lg shadow-lg p-6 max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-center mb-6">🎮 Piedra, Papel o Tijera</h1>
            
            <div class="bg-gray-700 rounded-lg p-4 mb-6">
                <h2 class="text-xl font-bold mb-2">Tu Saldo Actual</h2>
                <p class="text-3xl font-bold text-yellow-400">S/ {{ number_format($saldoDisponible, 2) }}</p>
            </div>

            @if(session('error'))
                <div class="bg-red-600 text-white p-4 rounded-lg mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('mensaje'))
                <div class="bg-gray-700 rounded-lg p-4 mb-6">
                    <h3 class="text-xl font-bold mb-4">Resultado:</h3>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-gray-800 p-4 rounded-lg text-center">
                            <p class="text-lg mb-2">Tu elección:</p>
                            <div class="text-6xl mb-2">
                                @if(session('eleccion') == 'piedra') ✊
                                @elseif(session('eleccion') == 'papel') ✋
                                @elseif(session('eleccion') == 'tijera') ✌️
                                @endif
                            </div>
                            <p class="text-lg">{{ ucfirst(session('eleccion')) }}</p>
                        </div>
                        
                        <div class="bg-gray-800 p-4 rounded-lg text-center">
                            <p class="text-lg mb-2">La casa eligió:</p>
                            <div class="text-6xl mb-2">
                                @if(session('resultado') == 'piedra') ✊
                                @elseif(session('resultado') == 'papel') ✋
                                @elseif(session('resultado') == 'tijera') ✌️
                                @endif
                            </div>
                            <p class="text-lg">{{ ucfirst(session('resultado')) }}</p>
                        </div>
                    </div>
                    
                    <div class="text-center p-4 rounded-lg {{ session('gano') ? 'bg-green-600' : (session('empate') ? 'bg-yellow-600' : 'bg-red-600') }}">
                        <p class="text-xl font-bold">{{ session('mensaje') }}</p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('piedrapapeltijera.jugar') }}">
                @csrf
                <div class="mb-6">
                    <h3 class="text-xl font-bold mb-4">Elige tu jugada:</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="bg-gray-700 p-4 rounded-lg text-center cursor-pointer hover:bg-blue-600 transition">
                            <input type="radio" name="eleccion" value="piedra" class="hidden" required>
                            <div class="text-6xl mb-2">✊</div>
                            <span class="text-lg font-bold">Piedra</span>
                        </label>
                        
                        <label class="bg-gray-700 p-4 rounded-lg text-center cursor-pointer hover:bg-blue-600 transition">
                            <input type="radio" name="eleccion" value="papel" class="hidden">
                            <div class="text-6xl mb-2">✋</div>
                            <span class="text-lg font-bold">Papel</span>
                        </label>
                        
                        <label class="bg-gray-700 p-4 rounded-lg text-center cursor-pointer hover:bg-blue-600 transition">
                            <input type="radio" name="eleccion" value="tijera" class="hidden">
                            <div class="text-6xl mb-2">✌️</div>
                            <span class="text-lg font-bold">Tijera</span>
                        </label>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="apuesta" class="block text-lg font-bold mb-2">Tu apuesta (S/):</label>
                    <input type="number" name="apuesta" id="apuesta" min="1" max="{{ $saldoDisponible }}" step="1" class="w-full p-3 bg-gray-700 rounded-lg" required>
                    <p class="text-sm text-gray-400 mt-1">Mín: S/ 1.00 - Máx: S/ {{ number_format($saldoDisponible, 2) }}</p>
                </div>
                
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg disabled:opacity-50" {{ $saldoDisponible <= 0 ? 'disabled' : '' }}>
                    {{ $saldoDisponible <= 0 ? 'Sin saldo disponible' : '¡Jugar Ahora!' }}
                </button>
            </form>
            
            <div class="mt-8 bg-gray-700 rounded-lg p-4">
                <h3 class="text-xl font-bold mb-2">Reglas del juego:</h3>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Piedra vence a Tijera</li>
                    <li>Tijera vence a Papel</li>
                    <li>Papel vence a Piedra</li>
                    <li>Si ganas, recibes el doble de tu apuesta</li>
                    <li>Si empatas, recuperas tu apuesta</li>
                    <li>Si pierdes, pierdes el monto apostado</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Seleccionar opción al hacer clic en el contenedor
            const opciones = document.querySelectorAll('label');
            opciones.forEach(opcion => {
                opcion.addEventListener('click', function() {
                    // Reiniciar todos los contenedores
                    opciones.forEach(o => o.classList.remove('bg-blue-600'));
                    // Resaltar el seleccionado
                    this.classList.add('bg-blue-600');
                    // Marcar el radio button
                    this.querySelector('input[type="radio"]').checked = true;
                });
            });
        });
    </script>
</body>
</html>