<x-filament-panels::page>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #seccion-resultados,
            #seccion-resultados * {
                visibility: visible;
            }

            #seccion-resultados {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            #boton-imprimir {
                display: none;
            }

            .filament-header,
            .filament-sidebar {
                display: none;
            }

        }
    </style>

    <div class="space-y-6 no-print">
        <div class="p-6 bg-white rounded-xl shadow dark:bg-gray-800">
            <h2 class="text-xl font-bold mb-4">Filtrar Asistencias</h2>
            <form wire:submit="generarReporte">
                {{ $this->form }}

                <div class="mt-4" style="margin-top: 20px;">
                    <x-filament::button type="submit">
                        Generar Reporte
                    </x-filament::button>
                </div>
            </form>
        </div>

        @if(isset($data['empleado_id']) && $registros->count() > 0)
            <div id="seccion-resultados" class="p-6 bg-white rounded-xl shadow dark:bg-gray-800">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">Resultados</h2>
                    <button id="boton-imprimir" onclick="window.print()"
                        style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);"
                        class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Imprimir
                    </button>
                </div>

                {{ $this->table }}

                <div class="mt-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-lg">Total Horas Trabajadas:</span>
                        <span class="font-bold text-xl">{{ number_format($totalHoras, 2) }} horas</span>
                    </div>
                </div>
            </div>
        @elseif(isset($data['empleado_id']) && $registros->count() === 0)
            <div class="p-6 bg-white rounded-xl shadow dark:bg-gray-800">
                <div class="text-center py-4">
                    <p class="text-gray-500 dark:text-gray-400">No se encontraron registros con los filtros seleccionados.
                    </p>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>