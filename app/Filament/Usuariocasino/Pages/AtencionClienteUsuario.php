<?php

namespace App\Filament\Usuariocasino\Pages;

use App\Models\AtencionCliente;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
use Filament\Tables\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;  // Añadir importación
use Filament\Infolists\Infolist;  // Añadir importación
use Filament\Infolists\Concerns\InteractsWithInfolists;  // Añadir importación

class AtencionClienteUsuario extends Page implements HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $title = 'Soporte al Cliente';
    protected static ?string $navigationLabel = 'Soporte';
    protected static ?string $navigationGroup = 'Ayuda';
    protected static ?int $navigationSort = 5;

    // Define la vista para esta página
    protected static string $view = 'filament.usuariocasino.pages.atencion-cliente-usuario';

    // Variable para el formulario
    public ?array $data = [];

    // Método para cargar el formulario
    public function mount(): void
    {
        // Inicializar el formulario
        $this->form->fill([
            'asunto' => null,
            'tipo_consulta' => null,
            'descripcion' => null,
        ]);
    }

    // Definir el formulario para crear tickets de soporte
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('asunto')
                    ->label('Asunto')
                    ->required()
                    ->maxLength(100),
                    
                Select::make('tipo') // Cambiar de 'tipo_consulta' a 'tipo'
                    ->label('Tipo de Consulta')
                    ->options([
                        'consulta' => 'Problemas con la cuenta',
                        'queja' => 'Queja',
                        'soporte_tecnico' => 'Problema técnico',
                        'verificacion' => 'Verificación',
                        'financiero' => 'Problema financiero',
                        'sugerencia' => 'Sugerencia',
                    ])
                    ->required(),
                    
                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->required()
                    ->maxLength(1000)
                    ->rows(4)
                    ->helperText('Describe tu problema con el mayor detalle posible'),
            ])
            ->statePath('data');
    }

        // Nuevo método: Infolist para mostrar los detalles del ticket
        public function infolist(Infolist $infolist): Infolist
        {
            return $infolist
                ->schema([                      
                    TextEntry::make('asunto')
                        ->label('Asunto')
                        ->size(TextEntry\TextEntrySize::Large),
                    
                    TextEntry::make('fecha_apertura')
                        ->label('Fecha de apertura')
                        ->dateTime('d/m/Y H:i'),
                    
                    TextEntry::make('tipo')
                        ->label('Tipo de consulta')
                        ->badge()
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'consulta' => 'Consulta',
                            'queja' => 'Queja',
                            'soporte_tecnico' => 'Soporte Técnico',
                            'verificacion' => 'Verificación',
                            'financiero' => 'Financiero',
                            'sugerencia' => 'Sugerencia',
                            default => $state,
                        })
                        ->color(fn (string $state): string => match ($state) {
                            'consulta' => 'info',
                            'queja' => 'danger',
                            'soporte_tecnico' => 'warning',
                            'verificacion' => 'success',
                            'financiero' => 'primary',
                            'sugerencia' => 'secondary',
                            default => 'gray',
                        }),
                    
                    TextEntry::make('estado')
                        ->label('Estado')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'abierto' => 'warning',
                            'en_proceso' => 'info',
                            'resuelto' => 'success',
                            'cerrado' => 'gray',
                            'escalado' => 'danger',
                            default => 'gray',
                        }),
                    
                    
                    TextEntry::make('descripcion')
                        ->label('Descripción')
                        ->markdown()
                        ->columnSpanFull(),
                    
                    TextEntry::make('respuesta')
                        ->label('Respuesta del soporte')
                        ->markdown()
                        ->columnSpanFull()
                        ->visible(fn ($record) => $record && !empty($record->respuesta)),
                    
                    TextEntry::make('fecha_respuesta')
                        ->label('Fecha de respuesta')
                        ->dateTime('d/m/Y H:i')
                        ->visible(fn ($record) => $record && !empty($record->fecha_respuesta)),
                ])
                ->columns(2);
        }

    // Definir la tabla para mostrar los tickets del usuario
    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Mostrar solo los tickets del usuario autenticado
                AtencionCliente::query()
                    ->where('cliente_id', Auth::guard('cliente')->id())
                    ->orderBy('fecha_creacion', 'desc')
            )
            ->columns([
                TextColumn::make('id')
                    ->label('Ticket #')
                    ->sortable(),
                    
                TextColumn::make('asunto')
                    ->label('Asunto')
                    ->searchable()
                    ->limit(30),
                    
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'consulta' => 'Consulta',
                        'queja' => 'Queja',
                        'soporte_tecnico' => 'Soporte Técnico',
                        'verificacion' => 'Verificación',
                        'financiero' => 'Financiero',
                        'sugerencia' => 'Sugerencia',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'consulta' => 'info',
                        'queja' => 'danger',
                        'soporte_tecnico' => 'warning',
                        'verificacion' => 'success',
                        'financiero' => 'primary',
                        'sugerencia' => 'secondary',
                        default => 'gray',
                    }),
                    
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'abierto' => 'warning',
                        'en_proceso' => 'info',
                        'resuelto' => 'success',
                        'cerrado' => 'gray',
                        'escalado' => 'danger',
                        default => 'gray',
                    }),
                    
                TextColumn::make('respuesta')
                    ->label('Respuesta')
                    ->limit(40),

            ])

            ->actions([
                // Añadir botón para ver detalle del ticket
                ViewAction::make('ver_ticket')
                    ->label('Ver detalle')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detalle del ticket')
                    ->modalWidth('2xl')  // Modal más ancho
                    ->modalIcon('heroicon-o-chat-bubble-left-right')
                    ->infolist($this->infolist(...)),
            ])
            ->paginated([10, 25, 50])
            ->emptyStateHeading('No tienes tickets de soporte')
            ->emptyStateDescription('Crea tu primer ticket de soporte usando el formulario de arriba.')
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right');
    }

    // Método para crear un nuevo ticket de soporte
    public function crearTicket()
    {
        // Validar y obtener los datos del formulario
        $data = $this->form->getState();
        
        try {
            DB::beginTransaction();
            
            // Crear un nuevo ticket
            $ticket = new AtencionCliente();
            $ticket->cliente_id = Auth::guard('cliente')->id();
            $ticket->asunto = $data['asunto'];
            $ticket->tipo = $data['tipo']; // Cambiar de 'tipo_consulta' a 'tipo'
            $ticket->descripcion = $data['descripcion'];
            $ticket->estado = 'abierto'; // Usar 'abierto' en lugar de 'pendiente'
            $ticket->fecha_apertura = now(); // Usar 'fecha_apertura' en lugar de 'fecha_creacion'
            $ticket->save();
            
            DB::commit();
            
            // Notificación de éxito
            Notification::make()
                ->title('Ticket creado')
                ->body('Tu ticket de soporte ha sido registrado. Te responderemos a la brevedad.')
                ->success()
                ->send();
            
            // Limpiar el formulario
            $this->form->fill([
                'asunto' => null,
                'tipo' => null, // Cambiar aquí también
                'descripcion' => null,
            ]);
            
            // Actualizar la tabla
            $this->refreshTable();
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Notificación de error
            Notification::make()
                ->title('Error al crear el ticket')
                ->body('Hubo un problema al registrar tu ticket: ' . $e->getMessage())
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