<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-6 bg-primary-500/10 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-blue-500/10 rounded-lg">
                <div class="bg-primary-600 p-6 rounded-lg shadow-lg text-white">
                    <h2 class="text-xl font-bold mb-2">Tu Saldo Actual</h2>
                    <p class="text-3xl font-bold">S/ {{ number_format($saldoDisponible, 2) }}</p>
                </div>
                <div class="bg-secondary-600 p-6 rounded-lg shadow-lg text-white">
                    <h2 class="text-xl font-bold mb-2">Tu Membresía</h2>
                    <p class="text-2xl font-bold">{{ $membresiaActual ? $membresiaActual->nombre : 'Sin membresía' }}</p>
                    @if(!$membresiaActual)
                    <p class="mt-2 text-sm">Adquiere una membresía para acceder a más juegos.</p>
                    @endif
                </div>
            </div>

            @php
            $categorias = $this->getJuegosPorCategoria();
            @endphp

            @foreach($categorias as $categoria => $juegosEnCategoria)
            <!-- Contenedor de categoría con Alpine.js para minimizar/maximizar -->
            <div class="mb-6" x-data="{ expanded: true }">
                <!-- Cabecera de categoría clicable -->
                <div @click="expanded = !expanded" class="flex items-center justify-between cursor-pointer mb-3 mt-3">
                    <h2 class="text-xl font-bold px-3 py-1.5 bg-blue-600 text-white rounded-lg inline-flex items-center">
                        {{ $categoria }}
                        <span class="ml-2 text-xs bg-blue-700 px-2 py-0.5 rounded-full">{{ count($juegosEnCategoria) }}</span>
                    </h2>
                    <button class="p-1 rounded-full hover:bg-blue-600/20 transition-colors duration-200">
                        <svg x-bind:class="{'rotate-180': !expanded}" class="w-5 h-5 transform transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>

                <!-- Contenido de la categoría con transición -->
                <div x-show="expanded"
                    x-transition:enter="transition-all ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-4"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition-all ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-4">
                    <!-- ACTUALIZADO: Grid con más columnas y gap menor -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                        @foreach($juegosEnCategoria as $juego)
                        <!-- ACTUALIZADO: Cards más compactos -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow hover:shadow-lg transition-all duration-300 flex flex-col h-full">
                            <!-- Imagen del juego reducida en altura -->
                            <div class="relative h-36 overflow-hidden">
                                @if(!empty($juego->imagen_url))
                                <img src="{{ $juego->imagen_url }}" alt="{{ $juego->nombre }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                    onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="hidden w-full h-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center">
                                    <span class="text-gray-500 dark:text-gray-400">{{ substr($juego->nombre, 0, 1) }}</span>
                                </div>
                                @elseif($juego->imagen)
                                <img src="{{ asset('storage/' . $juego->imagen) }}" alt="{{ $juego->nombre }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                @else
                                <div class="w-full h-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center">
                                    <span class="text-gray-500 dark:text-gray-400">{{ substr($juego->nombre, 0, 1) }}</span>
                                </div>
                                @endif

                                <!-- Requisito de membresía -->
                                @if($juego->membresia_requerida)
                                <div class="absolute top-1 right-1 px-1.5 py-0.5 bg-yellow-500 text-white text-xs font-bold rounded">
                                    Premium
                                </div>
                                @endif
                            </div>

                            <!-- Contenido del juego (más compacto) -->
                            <div class="p-3 flex-grow">
                                <h3 class="font-bold text-base mb-1 truncate">{{ $juego->nombre }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 text-xs line-clamp-2">
                                    {{ $juego->descripcion ?? 'Sin descripción disponible' }}
                                </p>
                            </div>

                            <!-- Botón de juego (más pequeño) -->
                            <div class="p-2 border-t border-gray-200 dark:border-gray-700">
                                <button
                                    wire:click="jugar({{ $juego->id }})"
                                    @if(!$this->puedeJugar($juego->id)) disabled @endif
                                    class="w-full rounded font-medium py-1.5 px-3 text-sm transition-colors duration-200
                                    @if($this->puedeJugar($juego->id))
                                    bg-blue-600 hover:bg-blue-700 text-white
                                    @else
                                    bg-gray-300 cursor-not-allowed text-gray-600 dark:bg-gray-700 dark:text-gray-400
                                    @endif"
                                    >
                                    @if($this->puedeJugar($juego->id))
                                    ¡Jugar ahora!
                                    @else
                                    Requiere membresía
                                    @endif
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach

            @if(count($categorias) == 0)
            <div class="text-center py-8">
                <div class="text-gray-400 dark:text-gray-500">
                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-xl font-medium">No hay juegos disponibles</h3>
                    <p class="mt-1">Vuelve pronto para ver nuevos juegos.</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar manejo de errores para imágenes
            document.querySelectorAll('img').forEach(img => {
                img.addEventListener('error', function() {
                    if (this.nextElementSibling && this.nextElementSibling.classList.contains('hidden')) {
                        this.style.display = 'none';
                        this.nextElementSibling.style.display = 'flex';
                    }
                });
            });
        });
    </script>
</x-filament-panels::page>