<x-filament-panels::page>
    <div class="space-y-6">
        <div class="p-6 bg-primary-500/10 rounded-xl">

            <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
                <form wire:submit="crearTicket">
                    {{ $this->form }}

                    <div class="mt-4" style="margin-top: 20px;">
                        <x-filament::button type="submit" size="lg">
                            Enviar Consulta
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>

        <div class="p-6 bg-gray-100 dark:bg-gray-800 rounded-xl">
            <h2 class="text-xl font-bold mb-4">Historial de Tickets</h2>
            {{ $this->table }}
        </div>

        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
            <h3 class="text-lg font-medium mb-3">Información Importante</h3>
            <ul class="list-disc list-inside space-y-2 text-gray-600 dark:text-gray-400">
                <li>Los tickets son atendidos en horario de oficina (Lun-Vie 9:00 AM - 6:00 PM).</li>
                <li>El tiempo promedio de respuesta es de 24 horas hábiles.</li>
                <li>Para problemas urgentes, también puedes contactarnos vía WhatsApp al +51 999-888-777.</li>
                <li>Recibirás notificaciones por correo electrónico cuando actualicemos tu ticket.</li>
            </ul>
        </div>
    </div>
</x-filament-panels::page>