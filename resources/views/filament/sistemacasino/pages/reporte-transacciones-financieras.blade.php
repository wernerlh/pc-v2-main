<!-- filepath: resources/views/filament/sistemacasino/pages/reporte-transacciones-financieras.blade.php -->
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
            .fi-btn,
            .no-print {
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
            th, td {
                padding: 8px;
                border: 1px solid #ddd;
            }
            th {
                background-color: #f2f2f2 !important;
                color: black !important;
            }
            
            .totales-separados {
                page-break-inside: avoid;
                margin-top: 20px;
            }
        }
    </style>

    <div class="space-y-6">
        <div class="p-6 bg-white rounded-xl shadow dark:bg-gray-800 no-print">
            <h2 class="text-xl font-bold mb-4">Filtrar Transacciones Online</h2>
            <form wire:submit="generarReporte">
                {{ $this->form }}
                
                <div class="mt-4" style="margin-top: 20px;">
                    <x-filament::button type="submit">
                        Generar Reporte
                    </x-filament::button>
                </div>
            </form>
        </div>
        
        @if(isset($data['fecha_inicio']) && count($registros) > 0)
            <div id="seccion-resultados" class="p-6 bg-white rounded-xl shadow dark:bg-gray-800">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">
                        @if($data['tipo'] == 'deposito')
                            Depósitos Online Completados
                        @elseif($data['tipo'] == 'retiro')
                            Retiros Online Completados
                        @else
                            Transacciones Online Completadas
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
                
                <div class="mb-4 print-block">
                    <h1 class="text-2xl font-bold text-center">
                        @if($data['tipo'] == 'deposito')
                            Reporte de Depósitos Online
                        @elseif($data['tipo'] == 'retiro')
                            Reporte de Retiros Online
                        @else
                            Reporte de Transacciones Online
                        @endif
                    </h1>
                    <p class="text-center text-gray-500">
                        Período: {{ \Carbon\Carbon::parse($data['fecha_inicio'])->format('d/m/Y') }} - 
                        {{ \Carbon\Carbon::parse($data['fecha_fin'])->format('d/m/Y') }}
                    </p>
                    @if(isset($data['cliente_id']))
                        <p class="text-center text-gray-500">
                            Cliente: {{ \App\Models\UserCliente::find($data['cliente_id'])->nombre_completo }}
                        </p>
                    @endif
                </div>
                
                {{ $this->table }}
                
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 totales-separados">
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-lg text-green-700 dark:text-green-400">Total Depósitos:</span>
                            <span class="font-bold text-xl text-green-700 dark:text-green-400">S/ {{ number_format($totalDepositos, 2) }}</span>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-lg text-red-700 dark:text-red-400">Total Retiros:</span>
                            <span class="font-bold text-xl text-red-700 dark:text-red-400">S/ {{ number_format($totalRetiros, 2) }}</span>
                        </div>
                    </div>

                    <div class="p-4 md:col-span-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-lg text-blue-700 dark:text-blue-400">Balance Neto:</span>
                            <span class="font-bold text-xl {{ ($totalDepositos - $totalRetiros) >= 0 ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                                S/ {{ number_format($totalDepositos - $totalRetiros, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(isset($data['fecha_inicio']) && count($registros) === 0)
            <div class="p-6 bg-white rounded-xl shadow dark:bg-gray-800 no-print">
                <div class="text-center py-4">
                    <p class="text-gray-500 dark:text-gray-400">No se encontraron transacciones con los filtros seleccionados.</p>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Script para añadir una cabecera al imprimir
        window.onbeforeprint = function() {
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
                const tipoTexto = tipoSelect ? 
                    (tipoSelect.value === 'deposito' ? 'Depósitos Online' : 
                     tipoSelect.value === 'retiro' ? 'Retiros Online' : 'Transacciones Online') : 
                    'Transacciones Online';
                
                // Obtener información del cliente
                const clienteSelect = document.querySelector('select[name="data.cliente_id"]');
                const clienteNombre = clienteSelect && clienteSelect.selectedIndex > 0 ? 
                    clienteSelect.options[clienteSelect.selectedIndex].text : 'Todos los clientes';
                
                const fechaInicio = document.querySelector('input[name="data.fecha_inicio"]')?.value || '';
                const fechaFin = document.querySelector('input[name="data.fecha_fin"]')?.value || '';
                
                // Crear contenido de la cabecera

                
                const resultados = document.getElementById('seccion-resultados');
                if (resultados) {
                    resultados.insertBefore(header, resultados.firstChild);
                }
            }
        };
        
        window.onafterprint = function() {
            // Eliminar cabecera después de imprimir
            const header = document.getElementById('print-header');
            if (header) {
                header.remove();
            }
        };
    </script>
</x-filament-panels::page>