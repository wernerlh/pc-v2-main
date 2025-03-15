<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AtencionClienteResource\Pages;
use App\Models\AtencionCliente;
use App\Models\Empleados;
use App\Models\UserCliente;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AtencionClienteResource extends Resource
{
    protected static ?string $model = AtencionCliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Atención al Cliente';
    protected static ?string $modelLabel = 'Ticket';
    protected static ?string $pluralModelLabel = 'Tickets';
    protected static ?string $navigationGroup = 'Gestión de Clientes';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('cliente_id')
                    ->label('Cliente')
                    ->options(UserCliente::all()->pluck('nombre_completo', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('empleado_id')
                    ->label('Empleado asignado')
                    ->options(Empleados::all()->pluck('nombre_completo', 'empleado_id'))
                    ->searchable()
                    ->nullable()
                    ->default(null) // Add this line
                    ->preload(), // Add this line

                DatePicker::make('fecha_apertura')
                    ->label('Fecha de Apertura')
                    ->required(),
                DatePicker::make('fecha_cierre')
                    ->label('Fecha de Cierre'),
                Select::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'consulta' => 'Consulta',
                        'queja' => 'Queja',
                        'soporte_tecnico' => 'Soporte Técnico',
                        'verificacion' => 'Verificación',
                        'financiero' => 'Financiero',
                        'sugerencia' => 'Sugerencia',
                    ])
                    ->required(),
                TextInput::make('asunto')
                    ->label('Asunto')
                    ->required()
                    ->maxLength(255),
                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->required()
                    ->columnSpanFull(),
                Select::make('prioridad')
                    ->label('Prioridad')
                    ->options([
                        'baja' => 'Baja',
                        'media' => 'Media',
                        'alta' => 'Alta',
                        'critica' => 'Crítica',
                    ])
                    ->default('media')
                    ->required(),
                Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'abierto' => 'Abierto',
                        'en_proceso' => 'En Proceso',
                        'resuelto' => 'Resuelto',
                        'cerrado' => 'Cerrado',
                        'escalado' => 'Escalado',
                    ])
                    ->default('abierto')
                    ->required(),
                MarkdownEditor::make('respuesta')
                    ->label('Respuesta')
                    ->columnSpanFull(),
                TextInput::make('tiempo_respuesta')
                    ->label('Tiempo de Respuesta (minutos)'),
                TextInput::make('calificacion')
                    ->label('Calificación'),
                Textarea::make('comentario_calificacion')
                    ->label('Comentario de Calificación')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('cliente.nombre_completo')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('empleado.nombre_completo')
                    ->label('Empleado')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('fecha_apertura')
                    ->label('Fecha de Apertura')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('fecha_cierre')
                    ->label('Fecha de Cierre')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->sortable(),
                TextColumn::make('asunto')
                    ->label('Asunto')
                    ->sortable(),
                SelectColumn::make('prioridad')
                    ->label('Prioridad')
                    ->options([
                        'baja' => 'Baja',
                        'media' => 'Media',
                        'alta' => 'Alta',
                        'critica' => 'Crítica',
                    ]),
                SelectColumn::make('estado')
                    ->label('Estado')
                    ->options([
                        'abierto' => 'Abierto',
                        'en_proceso' => 'En Proceso',
                        'resuelto' => 'Resuelto',
                        'cerrado' => 'Cerrado',
                        'escalado' => 'Escalado',
                    ]),
                TextColumn::make('tiempo_respuesta')
                    ->label('Tiempo de Respuesta (minutos)'),
                TextColumn::make('calificacion')
                    ->label('Calificación'),
            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->options([
                        'consulta' => 'Consulta',
                        'queja' => 'Queja',
                        'soporte_tecnico' => 'Soporte Técnico',
                        'verificacion' => 'Verificación',
                        'financiero' => 'Financiero',
                        'sugerencia' => 'Sugerencia',
                    ]),
                SelectFilter::make('prioridad')
                    ->options([
                        'baja' => 'Baja',
                        'media' => 'Media',
                        'alta' => 'Alta',
                        'critica' => 'Crítica',
                    ]),
                SelectFilter::make('estado')
                    ->options([
                        'abierto' => 'Abierto',
                        'en_proceso' => 'En Proceso',
                        'resuelto' => 'Resuelto',
                        'cerrado' => 'Cerrado',
                        'escalado' => 'Escalado',
                    ]),
            ])
            ->actions([])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [

            'index' => Pages\ListAtencionClientes::route('/'),
            'create' => Pages\CreateAtencionCliente::route('/create'),
            'edit' => Pages\EditAtencionCliente::route('/{record}/edit'),
        ];
    }
}
