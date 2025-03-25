<?php

namespace App\Filament\Sistemacasino\Pages;

use App\Models\TransaccionesCasinoP;
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
use Carbon\Carbon;

class ReporteTransaccionesCasino extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Transacciones Casino';
    protected static ?string $title = 'Reporte de Transacciones en Casino Físico';
    protected static ?string $navigationGroup = 'Reportes';
    protected static ?int $navigationSort = 8;

    protected static string $view = 'filament.sistemacasino.pages.reporte-transacciones-casino';

    public ?array $data = [];
    public $registros = [];
    public $totalDepositos = 0;
    public $totalRetiros = 0;

    public function mount(): void
    {
        $this->form->fill([
            'tipo' => 'todos',
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
                    ->options(UserCliente::query()->pluck('nombre_completo', 'id'))
                    ->searchable()
                    ->preload()
                    ->placeholder('Todos los clientes'),

                Select::make('sucursal_id')
                    ->label('Sucursal')
                    ->options(Sucursales::query()->pluck('nombre', 'id'))
                    ->searchable()
                    ->preload()
                    ->placeholder('Todas las sucursales'),

                Select::make('tipo')
                    ->label('Tipo de transacción')
                    ->options([
                        'todos' => 'Todos los tipos',
                        'deposito' => 'Solo Depósitos',
                        'retiro' => 'Solo Retiros',
                    ])
                    ->default('todos')
                    ->required(),

                DatePicker::make('fecha_inicio')
                    ->label('Desde')
                    ->required()
                    ->default(now()->startOfMonth()),

                DatePicker::make('fecha_fin')
                    ->label('Hasta')
                    ->required()
                    ->default(now())
                    ->after('fecha_inicio'),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TransaccionesCasinoP::query()
                    ->when(
                        isset($this->data['cliente_id']),
                        fn($query) => $query->where('cliente_id', $this->data['cliente_id'])
                    )
                    ->when(
                        isset($this->data['sucursal_id']),
                        fn($query) => $query->where('sucursal_id', $this->data['sucursal_id'])
                    )
                    ->when(
                        isset($this->data['tipo']) && $this->data['tipo'] !== 'todos',
                        fn($query) => $query->where('tipo', $this->data['tipo'])
                    )
                    ->when(
                        isset($this->data['fecha_inicio']) && isset($this->data['fecha_fin']),
                        fn($query) => $query->whereBetween('fecha', [
                            $this->data['fecha_inicio'],
                            $this->data['fecha_fin']
                        ])
                    )
            )
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('cliente.nombre_completo')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->sortable(),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'deposito' => 'success',
                        'retiro' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('PEN')
                    ->sortable(),

                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('metodo_pago')
                    ->label('Método de Pago')
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                TextColumn::make('referencia')
                    ->label('Referencia')
                    ->limit(20),
            ])
            ->defaultSort('fecha', 'desc');
    }

    public function generarReporte()
    {
        $this->validate();

        $query = TransaccionesCasinoP::with(['cliente', 'sucursal'])
            ->when(
                isset($this->data['cliente_id']),
                fn($query) => $query->where('cliente_id', $this->data['cliente_id'])
            )
            ->when(
                isset($this->data['sucursal_id']),
                fn($query) => $query->where('sucursal_id', $this->data['sucursal_id'])
            )
            ->when(
                isset($this->data['tipo']) && $this->data['tipo'] !== 'todos',
                fn($query) => $query->where('tipo', $this->data['tipo'])
            )
            ->when(
                isset($this->data['fecha_inicio']) && isset($this->data['fecha_fin']),
                fn($query) => $query->whereBetween('fecha', [
                    $this->data['fecha_inicio'],
                    $this->data['fecha_fin']
                ])
            );

        $this->registros = $query->get();

        // Calcular totales separados
        $this->totalDepositos = $this->registros
            ->where('tipo', 'deposito')
            ->sum('monto');

        $this->totalRetiros = $this->registros
            ->where('tipo', 'retiro')
            ->sum('monto');
    }
}