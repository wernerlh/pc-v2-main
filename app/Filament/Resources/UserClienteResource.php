<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserClienteResource\Pages;
use App\Models\Membresia;
use App\Models\UserCliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Forms\Components\Select;

use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DateTimePicker; // Cambiar el import



class UserClienteResource extends Resource
{
    protected static ?string $model = UserCliente::class;

    protected static ?string $navigationIcon = 'heroicon-c-user-circle'; // Icono para el menú de navegación
    protected static ?string $navigationLabel = 'Clientes'; // Etiqueta del menú
    protected static ?string $modelLabel = 'Cliente'; // Etiqueta singular
    protected static ?string $pluralModelLabel = 'Clientes'; // Etiqueta plural
    protected static ?string $navigationGroup = 'Gestión de Clientes'; // Grupo de navegación

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('estado_cuenta') // Nuevo campo para el estado de la cuenta
                    ->options([
                        'activa' => 'Activa',
                        'suspendida' => 'Suspendida',
                        'bloqueada' => 'Bloqueada',
                    ])
                    ->default('activa')
                    ->required()
                    ->live(), // Habilita la reactividad en tiempo real
                DateTimePicker::make('fecha_suspension')
                    ->label('Fecha de Suspensión')
                    ->nullable()
                    ->seconds() // Esto activa la selección de segundos
                    ->displayFormat('d/m/Y H:i:s') // Formato de visualización con segundos
                    ->native(false) // Usar el selector personalizado en lugar del nativo del navegador
                    ->hoursStep(1) // Paso para las horas (opcional)
                    ->minutesStep(1) // Paso para los minutos (opcional)
                    ->secondsStep(1) // Paso para los segundos (opcional)
                    ->visible(function (Forms\Get $get) {
                        return $get('estado_cuenta') === 'suspendida'; // Solo visible si el estado es "suspendida"
                    }),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('row_number')
                    ->label('N°')
                    ->rowIndex()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nombre de Usuario')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nombre_completo')
                    ->label('Nombre Y Apellido')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('documento_identidad')
                    ->label('DNI')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('direccion')
                    ->label('Dirección')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha_nacimiento')
                    ->date()
                    ->label('Fecha de Nacimiento')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('estado_cuenta') // Nuevo campo en la tabla
                    ->label('Estado de Cuenta'),

                TextColumn::make('fecha_suspension') // Nuevo campo en la tabla
                    ->label('Fecha de Suspensión')
                    ->dateTime('d/m/Y H:i:s T')
                    ->searchable()
                    ->sortable(),


                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Fecha de Creación')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label('Fecha de Actualización')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                // Filtros adicionales si es necesario
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
            'index' => Pages\ListUserClientes::route('/'),
            'create' => Pages\CreateUserCliente::route('/create'),
            'edit' => Pages\EditUserCliente::route('/{record}/edit'),
        ];
    }
}
