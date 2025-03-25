<?php

namespace App\Filament\Sistemacasino\Pages;

use App\Models\TransaccionesFinanciera;
use App\Models\UserCliente;
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

class ReporteTransaccionesFinancieras extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Transacciones Online';
    protected static ?string $title = 'Reporte de Transacciones Online';
    protected static ?string $navigationGroup = 'Reportes';
    protected static ?int $navigationSort = 7;

    protected static string $view = 'filament.sistemacasino.pages.reporte-transacciones-financieras';

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
                TransaccionesFinanciera::query()
                    ->when(
                        isset($this->data['tipo']) && $this->data['tipo'] != 'todos',
                        fn($query) => $query->where('tipo', $this->data['tipo'])
                    )
                    ->whereIn('estado', ['completada', 'completado'])
                    ->when(
                        isset($this->data['cliente_id']),
                        fn($query) => $query->where('cliente_id', $this->data['cliente_id'])
                    )
                    ->when(
                        isset($this->data['fecha_inicio']) && isset($this->data['fecha_fin']),
                        fn($query) => $query->whereBetween('fecha_solicitud', [
                            $this->data['fecha_inicio'],
                            $this->data['fecha_fin']
                        ])
                    )
            )
            ->columns([
                TextColumn::make('cliente.nombre_completo')
                    ->label('Cliente')
                    ->searchable(),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => $state == 'deposito' ? 'Depósito' : 'Retiro'),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('PEN'),

                TextColumn::make('banco')
                    ->label('Banco'),

                TextColumn::make('estado')
                    ->label('Estado de Transacción'),

                TextColumn::make('fecha_solicitud')
                    ->label('Fecha de Solicitud')
                    ->date('d/m/Y'),

                TextColumn::make('fecha_procesamiento')
                    ->label('Fecha de Procesamiento')
                    ->date('d/m/Y'),
            ]);
    }

    public function generarReporte()
    {
        $this->validate();

        $query = TransaccionesFinanciera::with('cliente')
            ->whereIn('estado', ['completada', 'completado'])
            ->when(
                isset($this->data['tipo']) && $this->data['tipo'] != 'todos',
                fn($query) => $query->where('tipo', $this->data['tipo'])
            )
            ->when(
                isset($this->data['cliente_id']),
                fn($query) => $query->where('cliente_id', $this->data['cliente_id'])
            )
            ->when(
                isset($this->data['fecha_inicio']) && isset($this->data['fecha_fin']),
                fn($query) => $query->whereBetween('fecha_solicitud', [
                    $this->data['fecha_inicio'],
                    $this->data['fecha_fin']
                ])
            );

        $this->registros = $query->get();
        
        // Calcular totales separados por tipo
        $this->totalDepositos = $this->registros->where('tipo', 'deposito')->sum('monto');
        $this->totalRetiros = $this->registros->where('tipo', 'retiro')->sum('monto');
    }
}