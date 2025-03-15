<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-6 bg-primary-500/10 rounded-xl">

            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-blue-500/10 rounded-lg">

                <div>
                <h2 class="text-xl font-bold mb-2">Registrar Nuevo Depósito</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-4">
                Completa el formulario para solicitar un nuevo depósito a tu cuenta.
            </p>
                </div>
                <div>
                    <h3 class="text-lg font-medium">Saldo disponible:</h3>
                    <p class="text-2xl font-bold">S/ {{ number_format($saldoDisponible, 2) }}</p>
                </div>

            </div>
            
            
            <form wire:submit="crearDeposito">
                {{ $this->form }}
                
                <div class="mt-4">
                    <x-filament::button type="submit" size="lg">
                        Solicitar Depósito
                    </x-filament::button>
                </div>
            </form>
        </div>
        
        <div class="p-6 bg-gray-100 dark:bg-gray-800 rounded-xl">
            <h2 class="text-xl font-bold mb-4">Historial de Depósitos</h2>
            {{ $this->table }}
        </div>
        
        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
            <h3 class="text-lg font-medium mb-3">Información Importante</h3>
            <ul class="list-disc list-inside space-y-2 text-gray-600 dark:text-gray-400">
                <li>Los depósitos son procesados en horario de oficina (Lun-Vie 9:00 AM - 6:00 PM).</li>
                <li>Una vez verificado tu depósito, el saldo será acreditado automáticamente a tu cuenta.</li>
                <li>Conserva el comprobante de tu operación hasta que se acredite el saldo.</li>
                <li>Los depósitos rechazados se muestran como referencia pero no afectan tu saldo disponible.</li>
                <li>Para consultas sobre el estado de tu depósito, contacta a soporte@casino.com</li>
            </ul>
        </div>
    </div>
</x-filament-panels::page>