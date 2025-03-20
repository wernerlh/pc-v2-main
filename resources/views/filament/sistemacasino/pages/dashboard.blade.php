<!-- filepath: resources/views/filament/sistemacasino/pages/dashboard.blade.php -->
<x-filament-panels::page>
    <!-- Tarjetas de estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
            <div class="flex items-center space-x-4">
                <div class="bg-primary-500/10 p-3 rounded-full">
                    <x-heroicon-o-user-group class="h-6 w-6 text-primary-500" />
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Clientes Registrados</h3>
                    <p class="text-3xl font-semibold text-gray-900 dark:text-white">{{ $totalClientes }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
            <div class="flex items-center space-x-4">
                <div class="bg-info-500/10 p-3 rounded-full">
                    <x-heroicon-o-user class="h-6 w-6 text-primary-500" />
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Empleados</h3>
                    <p class="text-3xl font-semibold text-gray-900 dark:text-white">{{ $totalEmpleados }}</p>
                </div>
            </div>
        </div>

        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
            <div class="flex items-center space-x-4">
                <div class="bg-warning-500/10 p-3 rounded-full">
                    <x-heroicon-o-shield-check class="h-6 w-6 text-primary-500" />
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total de Usuarios</h3>
                    <p class="text-3xl font-semibold text-gray-900 dark:text-white">{{ $totalUsuarios }}</p>
                </div>
            </div>
        </div>
    </div>

    <h1 class="text-2xl font-bold">Gráficos de Transacciones Online</h1>

    <!-- Gráficos de transacciones en la misma fila siempre -->
    <div class="grid grid-cols-2 md:grid-cols-2 gap-6 mb-6">
        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
            <h2 class="text-lg font-semibold mb-3">Depósitos (última semana)</h2>
            <div class="h-80">
                <canvas id="depositos-chart"></canvas>
            </div>
        </div>

        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
            <h2 class="text-lg font-semibold mb-3">Retiros (última semana)</h2>
            <div class="h-80">
                <canvas id="retiros-chart"></canvas>
            </div>
        </div>
    </div>

    <h1 class="text-2xl font-bold">Gráficos de Transacciones en el casino</h1>

    <!-- Gráficos de transacciones en la misma fila siempre -->
    <div class="grid grid-cols-2 md:grid-cols-2 gap-6 mb-6">
        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
            <h2 class="text-lg font-semibold mb-3">Depósitos (última semana)</h2>
            <div class="h-80">
                <canvas id="depositos-casino-chart"></canvas>
            </div>
        </div>

        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
            <h2 class="text-lg font-semibold mb-3">Retiros (última semana)</h2>
            <div class="h-80">
                <canvas id="retiros-casino-chart"></canvas>
            </div>
        </div>
    </div>

    <h1 class="text-2xl font-bold">Gráficos de Juegos más populares</h1>

    <!-- Gráficos de juegos y membresías -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-xl">
            <h2 class="text-lg font-semibold mb-3">Más populares</h2>
            <div class="h-80">
                <canvas id="juegos-chart"></canvas>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Gráfico de depósitos
            new Chart(
                document.getElementById('depositos-chart').getContext('2d'),
                {
                    type: 'line',
                    data: @json($transaccionesData['depositos']),
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                }
            );

            // Gráfico de retiros
            new Chart(
                document.getElementById('retiros-chart').getContext('2d'),
                {
                    type: 'line',
                    data: @json($transaccionesData['retiros']),
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                }
            );

            // Gráfico de depósitos en casino físico
            new Chart(
                document.getElementById('depositos-casino-chart').getContext('2d'),
                {
                    type: 'line',
                    data: @json($transaccionesCasinoPData['depositos']),
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                }
            );

            // Gráfico de retiros en casino físico
            new Chart(
                document.getElementById('retiros-casino-chart').getContext('2d'),
                {
                    type: 'line',
                    data: @json($transaccionesCasinoPData['retiros']),
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                }
            );

            // Gráfico de juegos más populares
            new Chart(
                document.getElementById('juegos-chart').getContext('2d'),
                {
                    type: 'bar',
                    data: @json($juegosData),
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Juegos más populares (veces jugado)'
                            }
                        }
                    }
                }
            );

            // Gráfico de membresías
            new Chart(
                document.getElementById('membresias-chart').getContext('2d'),
                {
                    type: 'doughnut',
                    data: @json($membresiasData),
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                }
            );
        });
    </script>
</x-filament-panels::page>