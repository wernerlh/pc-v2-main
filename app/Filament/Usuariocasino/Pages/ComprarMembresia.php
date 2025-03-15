<?php

namespace App\Filament\Usuariocasino\Pages;

use App\Models\BilleteraCliente;
use App\Models\ClienteMembresia;
use App\Models\Membresia;
use App\Services\BilleteraService;
use Filament\Forms\Components\Select;
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
use Carbon\Carbon;

class ComprarMembresia extends Page implements HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $title = 'Comprar Membresía';
    protected static ?string $navigationLabel = 'Comprar Membresía';
    protected static ?string $navigationGroup = 'Membresías';
    protected static ?int $navigationSort = 1;

    // Define la vista para esta página
    protected static string $view = 'filament.usuariocasino.pages.comprar-membresia';

    // Variable para el formulario
    public ?array $data = [];

    // Saldo disponible para mostrar en la vista
    public float $saldoDisponible = 0;

    // Método para obtener el servicio de billetera
    protected function getBilleteraService(): BilleteraService
    {
        return app(BilleteraService::class);
    }

    // Método para cargar el formulario
    public function mount(): void
    {

        // Verificar y actualizar membresías vencidas del usuario actual
        $this->verificarMembresiasVencidas();

        // Obtener el saldo disponible del usuario
        $user = Auth::guard('cliente')->user();
        $billetera = BilleteraCliente::where('cliente_id', $user->id)->first();
        $this->saldoDisponible = $billetera ? $billetera->balance_real : 0;

        // Inicializar el formulario
        $this->form->fill([
            'membresia_id' => null,
        ]);
    }

    // Método para verificar las membresías vencidas
    public function verificarMembresiasVencidas(): void
    {
        $userId = Auth::guard('cliente')->id();

        // Actualizar el estado de las membresías vencidas del usuario actual
        ClienteMembresia::where('cliente_id', $userId)
            ->where('estado', 'activa')
            ->where('fecha_vencimiento', '<', Carbon::now())
            ->update(['estado' => 'vencida']);
    }

    // Definir el formulario para seleccionar membresía
    public function form(Form $form): Form
    {
        // Obtener solo membresías activas y disponibles para compra
        $membresias = Membresia::all()->map(function ($membresia) {
            return [
                'id' => $membresia->id,
                'nombre' => $membresia->nombre . ' - S/ ' . number_format($membresia->precio, 2),
                'precio' => $membresia->precio,
            ];
        })->pluck('nombre', 'id')->toArray();

        // Obtener las membresías ya activas del usuario
        $userId = Auth::guard('cliente')->id();
        $membresiasActivas = ClienteMembresia::where('cliente_id', $userId)
            ->where('estado', 'activa')
            ->pluck('membresia_id')
            ->toArray();

        return $form
            ->schema([
                Select::make('membresia_id')
                    ->label('Seleccionar Membresía')
                    ->options($membresias)
                    ->disableOptionWhen(function ($value) use ($membresiasActivas) {
                        // Deshabilitar opciones que ya tengan una membresía activa
                        return in_array($value, $membresiasActivas);
                    })
                    ->helperText('Las membresías ya activas aparecen deshabilitadas.')
                    ->required(),
            ])
            ->statePath('data');
    }

    // Definir la tabla para mostrar las membresías del usuario
    public function table(Table $table): Table
    {

        // Verificar membresías vencidas antes de mostrar la tabla
        $this->verificarMembresiasVencidas();

        return $table
            ->query(
                // Mostrar solo las membresías del usuario autenticado
                ClienteMembresia::query()
                    ->where('cliente_id', Auth::guard('cliente')->id())
                    ->orderBy('fecha_inicio', 'desc')
            )
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('membresia.nombre')
                    ->label('Membresía')
                    ->searchable(),

                TextColumn::make('membresia.precio')
                    ->label('Precio Pagado')
                    ->money('PEN'),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'activa' => 'success',
                        'vencida' => 'danger',
                        'suspendida' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('fecha_inicio')
                    ->label('Fecha de Inicio')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make('fecha_vencimiento')
                    ->label('Fecha de Vencimiento')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Fecha de Compra')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),

                TextColumn::make('tiempo_restante')
                    ->label('Tiempo Restante')
                    ->formatStateUsing(function ($record) {
                        if ($record->estado !== 'activa') {
                            return '';
                        }

                        $now = Carbon::now();
                        $vencimiento = Carbon::parse($record->fecha_vencimiento);

                        if ($now->gt($vencimiento)) {
                            return 'Expirada';
                        }

                        $diff = $now->diff($vencimiento);
                        return $diff->format('%i min, %s seg');
                    }),
            ])
            ->defaultSort('fecha_inicio', 'desc')
            ->paginated([10, 25, 50])
            ->emptyStateHeading('No tienes membresías actualmente')
            ->emptyStateDescription('Compra tu primera membresía utilizando el formulario de arriba.')
            ->emptyStateIcon('heroicon-o-star');
    }

    // Método para comprar una membresía
    public function comprarMembresia()
    {
        // Validar y obtener los datos del formulario
        $data = $this->form->getState();

        // Obtener el cliente actual
        $userId = Auth::guard('cliente')->id();

        try {
            DB::beginTransaction();

            // Obtener la membresía seleccionada
            $membresia = Membresia::find($data['membresia_id']);

            if (!$membresia) {
                Notification::make()
                    ->title('Membresía no encontrada')
                    ->body('La membresía seleccionada no existe.')
                    ->danger()
                    ->send();

                DB::rollBack();
                return;
            }

            // Verificar que tenga saldo suficiente
            $billetera = BilleteraCliente::where('cliente_id', $userId)->first();

            if (!$billetera || $billetera->balance_real < $membresia->precio) {
                Notification::make()
                    ->title('Saldo insuficiente')
                    ->body('No tienes saldo suficiente para comprar esta membresía.')
                    ->danger()
                    ->send();

                DB::rollBack();
                return;
            }

            // Verificar si ya tiene una membresía activa del mismo tipo
            $membresiaActiva = ClienteMembresia::where('cliente_id', $userId)
                ->where('membresia_id', $membresia->id)
                ->where('estado', 'activa')
                ->first();

            if ($membresiaActiva) {
                Notification::make()
                    ->title('Membresía ya adquirida')
                    ->body('Ya tienes esta membresía activa. Debes esperar a que venza para renovarla.')
                    ->warning()
                    ->send();

                DB::rollBack();
                return;
            }

            // Crear la nueva membresía - DURACIÓN DE 5 MINUTOS PARA PRUEBAS
            $nuevaMembresia = new ClienteMembresia();
            $nuevaMembresia->cliente_id = $userId;
            $nuevaMembresia->membresia_id = $membresia->id;
            $nuevaMembresia->fecha_inicio = Carbon::now();
            $nuevaMembresia->fecha_vencimiento = Carbon::now()->addMinutes(5); // 5 minutos para pruebas
            $nuevaMembresia->estado = 'activa';
            $nuevaMembresia->save();

            // Descontar el precio del saldo del usuario
            $billetera->balance_real -= $membresia->precio;
            $billetera->save();

            DB::commit();

            // Notificación de éxito
            Notification::make()
                ->title('Membresía adquirida')
                ->body('Has adquirido la membresía ' . $membresia->nombre . ' con éxito por 5 minutos.')
                ->success()
                ->send();

            // Actualizar el saldo disponible
            $this->saldoDisponible = $billetera->balance_real;

            // Limpiar el formulario
            $this->form->fill([
                'membresia_id' => null,
            ]);

            // Actualizar la tabla
            $this->refreshTable();
        } catch (\Exception $e) {
            DB::rollBack();

            // Notificación de error
            Notification::make()
                ->title('Error al comprar la membresía')
                ->body('Hubo un problema al procesar tu compra: ' . $e->getMessage())
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
