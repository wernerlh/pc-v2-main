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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Textarea;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                        'inactiva' => 'Inactiva',
                        'suspendida' => 'Suspendida',
                        'bloqueada' => 'Bloqueada',
                    ])
                    ->default('activa')
                    ->required()
                    ->live(), // Habilita la reactividad en tiempo real
                DatePicker::make('fecha_suspension') // Campo para la fecha de suspensión
                    ->label('Fecha de Suspensión')
                    ->nullable()
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
                    ->date()
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
