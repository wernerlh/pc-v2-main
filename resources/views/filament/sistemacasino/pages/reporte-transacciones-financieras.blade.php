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

            /* Ocultar elementos específicos dentro del reporte */
            .fi-ta-search-field,
            .fi-pagination,
            #boton-imprimir,
            .filament-header,
            .filament-sidebar,
            .fi-ta-empty-state,
            .fi-ta-actions,
            .fi-btn {
                display: none !important;
            }

            /* Estilos para mejorar la impresión */
            table {
                width: 100%;
                border-collapse: collapse;
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
            }

            th,
            td {
                padding: 8px;
                border: 1px solid #ddd;
            }

            th {
                background-color: #f2f2f2 !important;
                color: black !important;
            }
        }
    </style>

    <div class="space-y-6 no-print">
        <div class="p-6 bg-white rounded-xl shadow dark:bg-gray-800">
            <h2 class="text-xl font-bold mb-4">Filtrar Transacciones</h2>
            <form wire:submit="generarReporte">
                {{ $this->form }}

                <div class="mt-4" style="margin-top: 20px;">
                    <x-filament::button type="submit">
                        Generar Reporte
                    </x-filament::button>
                </div>
            </form>
        </div>

        @if(isset($data['fecha_inicio']) && $registros->count() > 0)
            <div id="seccion-resultados" class="p-6 bg-white rounded-xl shadow dark:bg-gray-800">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">
                        @if($data['tipo'] == 'deposito')
                            Depósitos Completados
                        @elseif($data['tipo'] == 'retiro')
                            Retiros Completados
                        @else
                            Transacciones Completadas
                        @endif
                    </h2>
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


            </div>
        @elseif(isset($data['fecha_inicio']) && $registros->count() === 0)
            <div class="p-6 bg-white rounded-xl shadow dark:bg-gray-800">
                <div class="text-center py-4">
                    <p class="text-gray-500 dark:text-gray-400">
                        @if($data['tipo'] == 'deposito')
                            No se encontraron depósitos completados con los filtros seleccionados.
                        @elseif($data['tipo'] == 'retiro')
                            No se encontraron retiros completados con los filtros seleccionados.
                        @else
                            No se encontraron transacciones completadas con los filtros seleccionados.
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Script para añadir una cabecera al imprimir
        window.onbeforeprint = function () {
            // Crear elementos de cabecera para la impresión si no existen
            if (!document.getElementById('print-header')) {
                const header = document.createElement('div');
                header.id = 'print-header';
                header.style.textAlign = 'center';
                header.style.marginBottom = '20px';
                header.style.padding = '10px';
                header.style.borderBottom = '1px solid #ddd';

                // Obtener información del tipo de transacción
                const tipoSelect = document.querySelector('select[name="data.tipo"]');
                const tipoTexto = tipoSelect ? (tipoSelect.value === 'deposito' ? 'Depósitos' : 'Retiros') : 'Transacciones';

                // Obtener información del cliente
                const clienteSelect = document.querySelector('select[name="data.cliente_id"]');
                const clienteNombre = clienteSelect && clienteSelect.selectedIndex > 0 ?
                    clienteSelect.options[clienteSelect.selectedIndex].text : 'Todos los clientes';

                const fechaInicio = document.querySelector('input[name="data.fecha_inicio"]')?.value || '';
                const fechaFin = document.querySelector('input[name="data.fecha_fin"]')?.value || '';

                header.innerHTML = `
                    <h1 style="font-size: 24px; margin-bottom: 5px;">Reporte de ${tipoTexto} Completados</h1>

                `;

                const resultados = document.getElementById('seccion-resultados');
                if (resultados) {
                    resultados.insertBefore(header, resultados.firstChild);
                }
            }
        };

        window.onafterprint = function () {
            // Eliminar cabecera después de imprimir
            const header = document.getElementById('print-header');
            if (header) {
                header.remove();
            }
        };
    </script>
</x-filament-panels::page>