<?php

namespace App\Filament\Usuariocasino\Pages;

use App\Models\TransaccionesFinanciera;
use App\Models\BilleteraCliente;
use App\Services\BilleteraService;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MisDepositos extends Page implements HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $title = 'Mis Depósitos';
    protected static ?string $navigationLabel = 'Mis Depósitos';
    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?int $navigationSort = 2;

    // Define la vista para esta página
    protected static string $view = 'filament.usuariocasino.pages.mis-depositos';

    // Variable para el formulario
    public ?array $data = [];

    // Saldo disponible para mostrar en la vista
    public float $saldoDisponible = 0;
    
    // Eliminar el constructor y usar un método para obtener el servicio
    protected function getBilleteraService(): BilleteraService
    {
        return app(BilleteraService::class);
    }

    // Método para cargar el formulario
    public function mount(): void
    {
        // Obtener el saldo disponible del usuario
        $user = Auth::guard('cliente')->user();
        $billetera = BilleteraCliente::where('cliente_id', $user->id)->first();
        $this->saldoDisponible = $billetera ? $billetera->balance_real : 0;
        
        // Inicializar el formulario
        $this->form->fill([
            'monto' => null,
            'banco' => null,
            'numero_cuenta_bancaria' => null,
            'titular_cuenta' => null,
            'referencia_transferencia' => null,
        ]);
    }

    // Definir el formulario para crear depósitos
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('monto')
                    ->label('Monto a depositar')
                    ->numeric()
                    ->minValue(10)
                    ->maxValue(10000)
                    ->required()
                    ->prefix('S/'),
                    
                Select::make('banco')
                    ->label('Banco')
                    ->options([
                        'bcp' => 'Banco de Crédito del Perú',
                        'interbank' => 'Interbank',
                        'bbva' => 'BBVA Continental',
                        'scotiabank' => 'Scotiabank',
                        'otro' => 'Otro',
                    ])
                    ->required(),
                    
                TextInput::make('numero_cuenta_bancaria')
                    ->label('Número de Cuenta Bancaria')
                    ->required()
                    ->maxLength(20),
                    
                TextInput::make('titular_cuenta')
                    ->label('Titular de la Cuenta')
                    ->required()
                    ->maxLength(100),
                    
                TextInput::make('referencia_transferencia')
                    ->label('Referencia de Transferencia')
                    ->required()
                    ->helperText('Número de operación o comprobante')
                    ->maxLength(50),
            ])
            ->statePath('data');
    }

    // Definir la tabla para mostrar los depósitos del usuario
    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Mostrar solo las transacciones del usuario autenticado
                TransaccionesFinanciera::query()
                    ->where('cliente_id', Auth::guard('cliente')->id())
                    ->where('tipo', 'deposito')
                    ->orderBy('fecha_solicitud', 'desc')
            )
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                    
                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('PEN')
                    ->sortable(),
                    
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'completada' => 'success',
                        'rechazada' => 'danger',
                        default => 'gray',
                    }),
                    
                TextColumn::make('banco')
                    ->label('Banco')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bcp' => 'BCP',
                        'interbank' => 'Interbank',
                        'bbva' => 'BBVA',
                        'scotiabank' => 'Scotiabank',
                        default => $state,
                    }),
                    
                TextColumn::make('referencia_transferencia')
                    ->label('Referencia')
                    ->limit(10),
                    
                TextColumn::make('fecha_solicitud')
                    ->label('Fecha de Solicitud')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                    
                TextColumn::make('fecha_procesamiento')
                    ->label('Fecha de Procesamiento')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                    
                TextColumn::make('motivo_rechazo')
                    ->label('Motivo de Rechazo')
                    ->visible(fn ($record) => $record && $record->estado === 'rechazado') // Verifica que $record no sea nulo
                    ->wrap(),
            ])
            ->defaultSort('fecha_solicitud', 'desc')
            ->paginated([10, 25, 50])
            ->emptyStateHeading('No tienes depósitos registrados')
            ->emptyStateDescription('Registra tu primer depósito usando el formulario de arriba.')
            ->emptyStateIcon('heroicon-o-banknotes');
    }

    // Método para crear un nuevo depósito
    public function crearDeposito()
    {
        // Validar y obtener los datos del formulario
        $data = $this->form->getState();
        
        try {
            DB::beginTransaction();
            
            // Crear una nueva transacción financiera
            $transaccion = new TransaccionesFinanciera();
            $transaccion->cliente_id = Auth::guard('cliente')->id();
            $transaccion->monto = $data['monto'];
            $transaccion->tipo = 'deposito';
            $transaccion->estado = 'pendiente';
            $transaccion->numero_cuenta_bancaria = $data['numero_cuenta_bancaria'];
            $transaccion->banco = $data['banco'];
            $transaccion->titular_cuenta = $data['titular_cuenta'];
            $transaccion->referencia_transferencia = $data['referencia_transferencia'];
            $transaccion->fecha_solicitud = now();
            $transaccion->save();
            
            // Usar el método getter para obtener el servicio
            $this->getBilleteraService()->registrarDepositoPendiente($transaccion);
            
            DB::commit();
            
            // Notificación de éxito
            Notification::make()
                ->title('Solicitud de depósito creada')
                ->body('Tu solicitud de depósito ha sido registrada y será procesada en breve.')
                ->success()
                ->send();
            
            // Limpiar el formulario
            $this->form->fill([
                'monto' => null,
                'numero_cuenta_bancaria' => null,
                'banco' => null,
                'titular_cuenta' => null,
                'referencia_transferencia' => null,
            ]);
            
            // Actualizar la tabla
            $this->refreshTable();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Notificación de error con más detalles para depuración
            Notification::make()
                ->title('Error al crear la solicitud')
                ->body('Hubo un problema al registrar tu solicitud: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
    
    // Método auxiliar para refrescar la tabla
    public function refreshTable()
    {
        $this->resetTable();
    }
}