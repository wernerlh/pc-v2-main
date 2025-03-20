<?php

namespace App\Filament\Sistemacasino\Pages;

use App\Models\AsistenciaEmpleados;
use App\Models\Empleados;
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

class ReporteAsistencia extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Asistencia Empleados';
    protected static ?string $title = 'Reporte de Asistencia';
    protected static ?string $navigationGroup = 'Reportes';
    protected static ?int $navigationSort = 6;
    
    protected static string $view = 'filament.sistemacasino.pages.reporte-asistencia';
    
    public ?array $data = [];
    public $registros = [];
    public $totalHoras = 0;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('empleado_id')
                    ->label('Empleado')
                    ->options(Empleados::query()->pluck('nombre_completo', 'empleado_id'))
                    ->searchable()
                    ->preload()
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
                AsistenciaEmpleados::query()
                    ->when(
                        isset($this->data['empleado_id']),
                        fn ($query) => $query->where('empleado_id', $this->data['empleado_id'])
                    )
                    ->when(
                        isset($this->data['fecha_inicio']) && isset($this->data['fecha_fin']),
                        fn ($query) => $query->whereBetween('fecha', [
                            $this->data['fecha_inicio'],
                            $this->data['fecha_fin']
                        ])
                    )
            )
            ->columns([
                TextColumn::make('empleado.nombre_completo')
                    ->label('Empleado')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('hora_entrada')
                    ->label('Hora Entrada')
                    ->time('H:i'),
                
                TextColumn::make('hora_salida')
                    ->label('Hora Salida')
                    ->time('H:i'),
                
                TextColumn::make('horas_trabajadas')
                    ->label('Horas')
                    ->numeric(
                        decimalPlaces: 2,
                        decimalSeparator: '.',
                        thousandsSeparator: ','
                    ),
                
                TextColumn::make('tipo_jornada')
                    ->label('Jornada'),
                
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PRESENTE' => 'success',
                        'AUSENTE' => 'danger',
                        'TARDANZA' => 'warning',
                        'PERMISO' => 'info',
                        default => 'gray',
                    }),
                
                TextColumn::make('observaciones')
                    ->label('Observaciones')
                    ->limit(30),
            ]);
    }

    public function generarReporte()
    {
        $this->validate();
        
        $this->registros = AsistenciaEmpleados::with('empleado')
            ->where('empleado_id', $this->data['empleado_id'])
            ->whereBetween('fecha', [
                $this->data['fecha_inicio'],
                $this->data['fecha_fin']
            ])
            ->get();
        
        $this->totalHoras = $this->registros->sum('horas_trabajadas');
    }
}