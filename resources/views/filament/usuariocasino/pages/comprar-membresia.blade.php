<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-6 bg-primary-500/10 rounded-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-blue-500/10 rounded-lg">
                <div>
                    <h2 class="text-xl font-bold mb-2">Comprar Membresía</h2>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        Selecciona una membresía para acceder a beneficios exclusivos y juegos premium.
                    </p>
                </div>
                <div class="bg-primary-600 p-6 rounded-lg shadow-lg text-white">
                    <h3 class="text-lg font-medium">Saldo disponible:</h3>
                    <p class="text-2xl font-bold">S/ {{ number_format($saldoDisponible, 2) }}</p>
                </div>
            </div>

            <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
                <form wire:submit="comprarMembresia">
                    {{ $this->form }}

                    <div class="mt-4" style="margin-top: 20px;">
                        <x-filament::button
                            type="submit"
                            size="lg"
                            :disabled="$saldoDisponible <= 0">
                            {{ $saldoDisponible <= 0 ? 'Saldo insuficiente' : 'Comprar Membresía' }}
                        </x-filament::button>
                    </div>
                </form>
            </div>


        </div>

        <div class="p-6 bg-gray-100 dark:bg-gray-800 rounded-xl">
            <h2 class="text-xl font-bold mb-4">Mis Membresías</h2>
            {{ $this->table }}
        </div>

        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
            <h3 class="text-lg font-medium mb-3">Información Importante</h3>
            <ul class="list-disc list-inside space-y-2 text-gray-600 dark:text-gray-400">
                <li>Las membresías tienen una duración de 5 minutos (solo para pruebas).</li>
                <li>Al vencer, su estado cambiará automáticamente a "vencida".</li>
                <li>No puedes comprar la misma membresía si ya tienes una activa.</li>
                <li>Cada membresía brinda diferentes beneficios y acceso a juegos exclusivos.</li>
                <li>Para consultas sobre tu membresía, contacta a soporte@casino.com</li>
            </ul>
        </div>
    </div>
</x-filament-panels::page>