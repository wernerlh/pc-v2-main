<?php

namespace App\Filament\Usuariocasino\Pages;

use App\Models\TransaccionesFinanciera;
use App\Models\BilleteraCliente;
use App\Services\BilleteraService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MisRetiros extends Page implements HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $title = 'Mis Retiros';
    protected static ?string $navigationLabel = 'Solicitar Retiro';
    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?int $navigationSort = 3;

    // Define la vista para esta página
    protected static string $view = 'filament.usuariocasino.pages.mis-retiros';

    // Variable para el formulario
    public ?array $data = [];
    
    // Saldo disponible para mostrar en la vista
    public float $saldoDisponible = 0;
    
    // Elimina el constructor personalizado
    
    // Método para obtener el servicio de billetera
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
        ]);
    }

    // Definir el formulario para crear retiros
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('monto')
                    ->label('Monto a retirar')
                    ->numeric()
                    ->minValue(50) // Monto mínimo para retiro
                    ->maxValue($this->saldoDisponible) // No puede retirar más de lo que tiene
                    ->required()
                    ->prefix('S/')
                    ->helperText('Mínimo: S/50.00 - Máximo: S/' . number_format($this->saldoDisponible, 2)),
                    
                Select::make('banco')
                    ->label('Banco para la transferencia')
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
                    ->maxLength(100)
                    ->helperText('El titular debe coincidir con el nombre en tu cuenta del casino'),
            ])
            ->statePath('data');
    }

    // Definir la tabla para mostrar los retiros del usuario
    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Mostrar solo las transacciones del usuario autenticado
                TransaccionesFinanciera::query()
                    ->where('cliente_id', Auth::guard('cliente')->id())
                    ->where('tipo', 'retiro')
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
                    
                TextColumn::make('numero_cuenta_bancaria')
                    ->label('Cuenta')
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
                    ->visible(fn ($record) => $record && $record->estado === 'rechazada')
                    ->wrap(),
            ])
            ->defaultSort('fecha_solicitud', 'desc')
            ->paginated([10, 25, 50])
            ->emptyStateHeading('No tienes retiros registrados')
            ->emptyStateDescription('Solicita tu primer retiro usando el formulario de arriba.')
            ->emptyStateIcon('heroicon-o-currency-dollar');
    }

    // Método para crear un nuevo retiro
    public function solicitarRetiro()
    {
        // Validar y obtener los datos del formulario
        $data = $this->form->getState();
        
        // Obtener el cliente actual
        $userId = Auth::guard('cliente')->id();
        
        try {
            DB::beginTransaction();
            
            // Verificar que tenga saldo suficiente (doble verificación)
            if (!$this->getBilleteraService()->puedeRetirar($userId, $data['monto'])) {
                Notification::make()
                    ->title('Saldo insuficiente')
                    ->body('No tienes saldo suficiente para realizar este retiro.')
                    ->danger()
                    ->send();
                    
                DB::rollBack();
                return;
            }
            
            // Crear una nueva transacción financiera
            $transaccion = new TransaccionesFinanciera();
            $transaccion->cliente_id = $userId;
            $transaccion->monto = $data['monto'];
            $transaccion->tipo = 'retiro';
            $transaccion->estado = 'pendiente';
            $transaccion->banco = $data['banco'];
            $transaccion->numero_cuenta_bancaria = $data['numero_cuenta_bancaria'];
            $transaccion->titular_cuenta = $data['titular_cuenta'];
            $transaccion->fecha_solicitud = now();
            $transaccion->save();
            
            // Reservar el monto para el retiro (reducir del balance disponible)
            $this->getBilleteraService()->reservarFondosParaRetiro($userId, $data['monto']);
            
            DB::commit();
            
            // Notificación de éxito
            Notification::make()
                ->title('Solicitud de retiro creada')
                ->body('Tu solicitud de retiro ha sido registrada y será procesada en breve.')
                ->success()
                ->send();
            
            // Actualizar el saldo disponible
            $billetera = BilleteraCliente::where('cliente_id', $userId)->first();
            $this->saldoDisponible = $billetera ? $billetera->balance_real : 0;
            
            // Limpiar el formulario
            $this->form->fill([
                'monto' => null,
                'banco' => null,
                'numero_cuenta_bancaria' => null,
                'titular_cuenta' => null,
            ]);
            
            // Actualizar la tabla
            $this->refreshTable();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Notificación de error
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