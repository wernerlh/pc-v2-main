<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        
        <div class="mt-8" style="margin-top: 20px;">
            <x-filament::button type="submit">
                Actualizar Contraseña
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>