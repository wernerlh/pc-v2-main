<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        
        <div class="mt-8">
            <x-filament::button type="submit">
                Actualizar Contraseña
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>