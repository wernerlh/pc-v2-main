<?php

namespace App\Filament\Sistemacasino\Pages;

use App\Models\JuegosOnline;
use App\Models\TransaccionesJuego;
use App\Models\UserCliente;
use App\Models\Sucursales;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class ReportePerdidaGananciaCliente extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Pérdidas y Ganancias Online';
    protected static ?string $title = 'Reporte de Pérdidas y Ganancias de Clientes';
    protected static ?string $navigationGroup = 'Reportes';
    protected static ?int $navigationSort = 9;

    protected static string $view = 'filament.sistemacasino.pages.reporte-perdidas-ganancias-cliente';

    public ?array $data = [];
    public $registros = [];
    public $totalPerdidas = 0;
    public $totalGanancias = 0;

    public function mount(): void
    {
        $this->form->fill([
            'fecha_inicio' => now()->startOfMonth()->format('Y-m-d'),
            'fecha_fin' => now()->format('Y-m-d'),
        ]);

        // Inicializar registros como array vacío
        $this->registros = [];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('cliente_id')
                    ->label('Cliente')
                    ->options(UserCliente::all()->pluck('nombre_completo', 'id'))
                    ->searchable(),
                
                Select::make('juego_id')
                    ->label('Juego')
                    ->options(JuegosOnline::all()->pluck('nombre', 'id'))
                    ->searchable(),
                
                DatePicker::make('fecha_inicio')
                    ->label('Fecha Inicio')
                    ->required()
                    ->default(now()->startOfMonth()),
                
                DatePicker::make('fecha_fin')
                    ->label('Fecha Fin')
                    ->required()
                    ->default(now())
                    ->after('fecha_inicio'),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('cliente.nombre_completo')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('juego.nombre')
                    ->label('Juego')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('monto_apostado')
                    ->label('Monto Apostado')
                    ->money('PEN')
                    ->sortable(),
                
                TextColumn::make('monto_ganado')
                    ->label('Monto Ganado')
                    ->money('PEN')
                    ->sortable(),
                
                TextColumn::make('perdida')
                    ->label('Pérdida')
                    ->formatStateUsing(fn ($state) => $state > 0 ? 'S/ ' . number_format($state, 2) : '-')
                    ->color('danger')
                    ->sortable(),
                
                TextColumn::make('ganancia')
                    ->label('Ganancia')
                    ->formatStateUsing(fn ($state) => $state > 0 ? 'S/ ' . number_format($state, 2) : '-')
                    ->color('success')
                    ->sortable(),
                
                TextColumn::make('fecha_hora')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->defaultSort('fecha_hora', 'desc');
    }

    public function getTableQuery()
    {
        if (!isset($this->data['fecha_inicio'])) {
            return TransaccionesJuego::query()->whereNull('id');
        }

        $query = TransaccionesJuego::query()
            ->select([
                'transacciones_juego.*',
                DB::raw('(monto_ganado - monto_apostado) as balance'),
                DB::raw('CASE WHEN (monto_ganado - monto_apostado) < 0 THEN ABS(monto_ganado - monto_apostado) ELSE 0 END as perdida'),
                DB::raw('CASE WHEN (monto_ganado - monto_apostado) > 0 THEN (monto_ganado - monto_apostado) ELSE 0 END as ganancia'),
            ])
            ->with(['cliente', 'juego'])
            ->whereBetween('fecha_hora', [ // Usando fecha_hora en lugar de fecha
                $this->data['fecha_inicio'] . ' 00:00:00',
                $this->data['fecha_fin'] . ' 23:59:59'
            ]);

        if (isset($this->data['cliente_id']) && $this->data['cliente_id']) {
            $query->where('cliente_id', $this->data['cliente_id']);
        }

        if (isset($this->data['juego_id']) && $this->data['juego_id']) {
            $query->where('juego_id', $this->data['juego_id']);
        }

        return $query;
    }

    public function generarReporte()
    {
        // Validación básica
        $this->validate([
            'data.fecha_inicio' => 'required|date',
            'data.fecha_fin' => 'required|date|after_or_equal:data.fecha_inicio',
        ]);

        // Depuración - Añadir para ver si hay problemas con la consulta
        \Illuminate\Support\Facades\Log::info('Generando reporte con filtros:', $this->data);

        $this->registros = $this->getTableQuery()->get();
        
        // Depuración - Ver cuántos registros retorna
        \Illuminate\Support\Facades\Log::info('Registros encontrados: ' . count($this->registros));
        
        // Calcular totales
        $this->totalPerdidas = $this->registros->sum('perdida');
        $this->totalGanancias = $this->registros->sum('ganancia');
        
        // Depuración - Verificar totales
        \Illuminate\Support\Facades\Log::info('Total pérdidas: ' . $this->totalPerdidas);
        \Illuminate\Support\Facades\Log::info('Total ganancias: ' . $this->totalGanancias);
    }
}