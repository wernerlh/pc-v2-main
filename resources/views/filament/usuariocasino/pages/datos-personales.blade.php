<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        
        <div class="mt-8" style="margin-top: 20px;">  <!-- Cambiamos de mt-4 a mt-8 para más espacio -->
            <x-filament::button type="submit">
                Guardar cambios
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>