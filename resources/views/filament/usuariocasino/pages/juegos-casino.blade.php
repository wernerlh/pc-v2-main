<!-- filepath: resources/views/filament/usuariocasino/pages/juegos-casino.blade.php -->
<div>
    <x-filament-panels::page>
        <div class="space-y-6">
            <!-- Información del saldo y membresía -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
            
            <!-- Juegos por categoría -->
            <div class="space-y-8">
                @foreach($this->getJuegosPorCategoria() as $categoria => $juegos)
                    <div>
                        <h3 class="text-xl font-bold mb-4 border-b pb-2">{{ $categoria }}</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($juegos as $juego)
                                <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden relative">
                                    <!-- Imagen del juego (placeholder) -->
                                    <div class="h-40 bg-gradient-to-br from-primary-500 to-secondary-500 flex items-center justify-center">
                                        <span class="text-3xl font-bold text-white">{{ substr($juego->nombre, 0, 1) }}</span>
                                    </div>
                                    
                                    <div class="p-4">
                                        <h4 class="text-lg font-bold truncate">{{ $juego->nombre }}</h4>
                                        
                                        <div class="my-2">
                                            <p class="text-sm text-gray-400 line-clamp-2">
                                                {{ $juego->descripcion ?: 'Sin descripción disponible' }}
                                            </p>
                                        </div>
                                        
                                        @if($juego->membresia_requerida)
                                            <div class="text-xs text-amber-500 mb-2">
                                                Requiere membresía: {{ $juego->membresiaRequerida->nombre ?? 'Especial' }}
                                            </div>
                                        @endif
                                        
                                        <div class="mt-4">
                                            @if($this->puedeJugar($juego->id))
                                                <x-filament::button 
                                                    size="sm" 
                                                    wire:click="jugar({{ $juego->id }})" 
                                                    color="success"
                                                    class="w-full">
                                                    Jugar ahora
                                                </x-filament::button>
                                            @else
                                                <x-filament::button 
                                                    size="sm" 
                                                    color="gray"
                                                    disabled
                                                    class="w-full">
                                                    Requiere membresía
                                                </x-filament::button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                
                @if(count($this->getJuegosPorCategoria()) === 0)
                    <div class="text-center py-12">
                        <div class="text-5xl mb-4">🎮</div>
                        <h3 class="text-xl font-medium">No hay juegos disponibles por el momento</h3>
                        <p class="text-gray-500 mt-2">Vuelve más tarde para ver nuevos juegos</p>
                    </div>
                @endif
            </div>
        </div>
    </x-filament-panels::page>
</div>