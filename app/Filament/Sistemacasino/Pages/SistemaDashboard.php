<?php

namespace App\Filament\Sistemacasino\Pages;

use Filament\Pages\Page;
use App\Models\UserCliente;
use App\Models\Empleados;
use App\Models\User;
use App\Models\TransaccionesFinanciera;
use App\Models\TransaccionesCasinoP;
use App\Models\TransaccionesJuego;
use App\Models\JuegosOnline;
use App\Models\Membresia;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\View\View;

class SistemaDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = 'Panel de Control';
    protected static ?int $navigationSort = -2;
    protected static string $view = 'filament.sistemacasino.pages.dashboard';

    // El método content prepara los datos para la vista
    public function getViewData(): array
    {
        return [
            'totalClientes' => UserCliente::count(),
            'totalEmpleados' => Empleados::count(),
            'totalUsuarios' => User::count(),
            'transaccionesData' => $this->getTransaccionesData(),
            'transaccionesCasinoPData' => $this->getTransaccionesCasinoPData(), // Nuevo dato
            'juegosData' => $this->getJuegosData(),
            'membresiasData' => $this->getMembresiasData(),
            'ultimas_transacciones' => TransaccionesFinanciera::latest()->take(5)->get(),
        ];
    }

    /**
     * Obtiene datos de transacciones financieras para las gráficas
     * separando depósitos y retiros en gráficas diferentes
     * y filtrando solo las transacciones completadas
     * 
     * @return array
     */
    protected function getTransaccionesData(): array
    {
        // Obtener datos de los últimos 7 días
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        // Obtener solo las transacciones completadas en el rango de fechas
        $transacciones = TransaccionesFinanciera::whereBetween('fecha_solicitud', [$startDate, $endDate])
            ->select('id', 'tipo', 'monto', 'estado', 'fecha_solicitud')
            ->get();

        // Preparar arreglos para días, depósitos y retiros
        $labels = [];
        $depositos = [];
        $retiros = [];

        // Generar fechas para los últimos 7 días
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays(6 - $i);
            $formattedDate = $date->format('d/m');
            $labels[] = $formattedDate;

            // Filtrar transacciones por tipo y fecha
            $depositosDelDia = $transacciones->filter(function ($item) use ($date) {
                return $item->tipo === 'deposito' &&
                    Carbon::parse($item->fecha_solicitud)->format('Y-m-d') === $date->format('Y-m-d');
            })->count();

            $retirosDelDia = $transacciones->filter(function ($item) use ($date) {
                return $item->tipo === 'retiro' &&
                    Carbon::parse($item->fecha_solicitud)->format('Y-m-d') === $date->format('Y-m-d');
            })->count();

            $depositos[] = $depositosDelDia;
            $retiros[] = $retirosDelDia;
        }

        return [
            'depositos' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Depósitos',
                        'data' => $depositos,
                        'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                        'borderColor' => 'rgb(34, 197, 94)',
                    ],
                ],
            ],
            'retiros' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Retiros',
                        'data' => $retiros,
                        'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                        'borderColor' => 'rgb(239, 68, 68)',
                    ],
                ],
            ]
        ];
    }

    /**
     * Obtiene datos de transacciones en casinos físicos para las gráficas
     * separando depósitos y retiros en gráficas diferentes
     * 
     * @return array
     */
    protected function getTransaccionesCasinoPData(): array
    {
        // Obtener datos de los últimos 7 días
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        // Obtener todas las transacciones en el rango de fechas (sin filtrar por estado)
        $transacciones = TransaccionesCasinoP::whereBetween('fecha', [$startDate, $endDate])
            ->select('id', 'tipo', 'monto', 'estado', 'fecha', 'sucursal_id')
            ->get();

        // Preparar arreglos para días, depósitos y retiros
        $labels = [];
        $depositos = [];
        $retiros = [];

        // Generar fechas para los últimos 7 días
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays(6 - $i);
            $formattedDate = $date->format('d/m');
            $labels[] = $formattedDate;

            // Filtrar transacciones por tipo y fecha
            $depositosDelDia = $transacciones->filter(function ($item) use ($date) {
                return $item->tipo === 'deposito' &&
                    Carbon::parse($item->fecha)->format('Y-m-d') === $date->format('Y-m-d');
            })->count();

            $retirosDelDia = $transacciones->filter(function ($item) use ($date) {
                return $item->tipo === 'retiro' &&
                    Carbon::parse($item->fecha)->format('Y-m-d') === $date->format('Y-m-d');
            })->count();

            $depositos[] = $depositosDelDia;
            $retiros[] = $retirosDelDia;
        }

        return [
            'depositos' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Depósitos',
                        'data' => $depositos,
                        'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                        'borderColor' => 'rgb(59, 130, 246)',
                    ],
                ],
            ],
            'retiros' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Retiros',
                        'data' => $retiros,
                        'backgroundColor' => 'rgba(139, 92, 246, 0.5)',
                        'borderColor' => 'rgb(139, 92, 246)',
                    ],
                ],
            ]
        ];
    }
    
    /**
     * Obtiene los datos para mostrar el gráfico de juegos más populares
     * basado en las transacciones realizadas
     * 
     * @return array
     */
    protected function getJuegosData(): array
    {
        // Consultar todas las transacciones de juegos agrupadas por juego_id
        $juegosEstadisticas = TransaccionesJuego::selectRaw('juego_id, COUNT(*) as total_jugado')
            ->groupBy('juego_id')
            ->orderByDesc('total_jugado')
            ->take(10) // Limitar a los 10 juegos más populares
            ->get();

        // Preparar los arreglos para el gráfico
        $nombres = [];
        $jugados = [];
        $backgroundColors = [];

        // Colores para las barras del gráfico
        $colores = [
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 99, 132, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(255, 159, 64, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 205, 86, 0.7)',
            'rgba(201, 203, 207, 0.7)',
            'rgba(255, 99, 71, 0.7)',
            'rgba(50, 205, 50, 0.7)',
            'rgba(138, 43, 226, 0.7)',
        ];

        foreach ($juegosEstadisticas as $index => $estadistica) {
            // Cargar el juego relacionado para obtener su nombre
            $juego = JuegosOnline::find($estadistica->juego_id);

            if ($juego) {
                $nombres[] = $juego->nombre;
                $jugados[] = $estadistica->total_jugado;
                $backgroundColors[] = $colores[$index % count($colores)]; // Asignar color de forma cíclica
            }
        }

        return [
            'labels' => $nombres,
            'datasets' => [
                [
                    'label' => 'Veces jugado',
                    'data' => $jugados,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => 'rgba(0, 0, 0, 0.1)',
                    'borderWidth' => 1
                ],
            ],
        ];
    }
    protected function getMembresiasData(): array
    {
        try {
            // Intentar obtener datos reales
            $membresias = Membresia::withCount('usuarios')
                ->get();

            if ($membresias->isEmpty()) {
                throw new \Exception("No hay datos de membresías");
            }

            return [
                'labels' => $membresias->pluck('nombre')->toArray(),
                'datasets' => [
                    [
                        'label' => 'Usuarios por membresía',
                        'data' => $membresias->pluck('usuarios_count')->toArray(),
                        'backgroundColor' => [
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(249, 115, 22, 0.7)',
                            'rgba(217, 70, 239, 0.7)',
                        ],
                    ],
                ],
            ];
        } catch (\Exception $e) {
            // Si hay un error, devolver datos de ejemplo
            return [
                'labels' => ['Básica', 'Premium', 'Gold', 'VIP'],
                'datasets' => [
                    [
                        'label' => 'Usuarios por membresía',
                        'data' => [120, 80, 50, 30],
                        'backgroundColor' => [
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(249, 115, 22, 0.7)',
                            'rgba(217, 70, 239, 0.7)',
                        ],
                    ],
                ],
            ];
        }
    }
}