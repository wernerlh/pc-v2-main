<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AtencionEmpleadoResource\Pages;
use App\Filament\Resources\AtencionEmpleadoResource\RelationManagers;
use App\Models\AtencionEmpleado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use App\Models\User;
use App\Models\Empleados;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;



class AtencionEmpleadoResource extends Resource
{
    protected static ?string $model = AtencionEmpleado::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Gestión de Empresa';

    protected static ?string $navigationLabel = 'Atención a Empleados';

    protected static ?string $pluralModelLabel = 'Atenciones a Empleados';

    protected static ?string $modelLabel = 'Atención a Empleado';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('empleado_id')
                    ->label('Empleado')
                    ->options(Empleados::pluck('nombre_completo', 'empleado_id')) // Cambiar 'id' a 'empleado_id'
                    ->searchable()
                    ->required(),

                Select::make('supervisor_id')
                    ->label('Supervisor Asignado')
                    ->options(Empleados::pluck('nombre_completo', 'empleado_id')) // Cambiar 'id' a 'empleado_id'
                    ->searchable()
                    ->nullable(),

                TextInput::make('asunto')
                    ->label('Asunto')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->required()
                    ->maxLength(1000)
                    ->columnSpanFull(),

                Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'en_proceso' => 'En Proceso',
                        'resuelto' => 'Resuelto',
                        'cancelado' => 'Cancelado',
                    ])
                    ->default('pendiente')
                    ->required(),

                Select::make('prioridad')
                    ->label('Prioridad')
                    ->options([
                        'baja' => 'Baja',
                        'media' => 'Media',
                        'alta' => 'Alta',
                        'urgente' => 'Urgente',
                    ])
                    ->default('media')
                    ->required(),
                    

                DateTimePicker::make('fecha_solicitud')
                    ->label('Fecha de Solicitud')
                    ->required()
                    ->default(now()),

                DateTimePicker::make('fecha_atencion')
                    ->label('Fecha de Atención')
                    ->nullable(),

                DateTimePicker::make('fecha_resolucion')
                    ->label('Fecha de Resolución')
                    ->nullable(),

                Textarea::make('solucion')
                    ->label('Solución')
                    ->nullable()
                    ->maxLength(1000)
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('empleado.nombre_completo')
                    ->label('Empleado')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('supervisor.nombre_completo')
                    ->label('Supervisor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('asunto')
                    ->label('Asunto')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pendiente' => 'Pendiente',
                        'en_proceso' => 'En Proceso',
                        'resuelto' => 'Resuelto',
                        'cancelado' => 'Cancelado',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'en_proceso' => 'info',
                        'resuelto' => 'success',
                        'cancelado' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('prioridad')
                    ->label('Prioridad')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'baja' => 'Baja',
                        'media' => 'Media',
                        'alta' => 'Alta',
                        'urgente' => 'Urgente',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'baja' => 'success',
                        'media' => 'info',
                        'alta' => 'warning',
                        'urgente' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('fecha_solicitud')
                    ->label('Solicitud')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('fecha_atencion')
                    ->label('Atención')
                    ->date('d/m/Y H:i')
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state ? date('d/m/Y H:i', strtotime($state)) : '-'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListAtencionEmpleados::route('/'),
            'create' => Pages\CreateAtencionEmpleado::route('/create'),
            'edit' => Pages\EditAtencionEmpleado::route('/{record}/edit'),
        ];
    }
}
