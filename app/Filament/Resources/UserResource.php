<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Models\Empleados;

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
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Support\Facades\Hash;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Usuarios Empleados';
    protected static ?string $modelLabel = 'Usuario Empleado';
    protected static ?string $pluralModelLabel = 'Usuarios Empleados';
    protected static ?string $navigationGroup = 'Gestión de Empresa';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('empleado_id')
                    ->label('Seleccione un Empleado')
                    ->relationship('empleado', 'nombre_completo') // Relación con el modelo Empleado
                    ->searchable()
                    ->unique(ignorable: fn($record) => $record) // Ignora el registro actual en validaciones de unicidad
                    ->required()
                    ->preload()
                    ->live() // Hace que el campo sea reactivo
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        // Obtiene el DNI del empleado seleccionado
                        $empleado = Empleados::find($state);
                        if ($empleado) {
                            // Nombre completo (nombre + apellido)
                            $nombreCompleto = $empleado->nombre_completo;

                            // Elimina espacios y tildes del nombre completo
                            $nombreSinEspacios = str_replace(' ', '', $nombreCompleto);
                            $nombreSinTildes = iconv('UTF-8', 'ASCII//TRANSLIT', $nombreSinEspacios);
                            $nombreLimpio = preg_replace('/[^a-zA-Z0-9]/', '', $nombreSinTildes);

                            // Obtiene los últimos 2 dígitos del año de nacimiento
                            $anioNacimiento = substr(date('Y', strtotime($empleado->fecha_nacimiento)), -2);

                            // Obtiene los últimos 2 dígitos del año de contratación
                            $anioContratacion = substr(date('Y', strtotime($empleado->fecha_contratacion)), -2);

                            // Genera 2 números aleatorios entre 0 y 99
                            $numerosAleatorios = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);

                            // Combina todo y convierte a minúsculas
                            $name = strtolower($nombreLimpio . $anioNacimiento . $anioContratacion . $numerosAleatorios);

                            // Actualiza el campo name
                            $set('name', $name);

                            // Actualiza el campo DNI y email                
                            $set('email', $empleado->correo);

                            $set('dni_empleado', $empleado->documento_identidad);
                        }
                    }),

                TextInput::make('name')
                    ->label('Usuario')
                    ->required()
                    ->maxLength(100)
                    ->disabledOn('edit')
                    ->dehydrated(true), // Asegura que el valor se guarde aunque esté deshabilitado,

                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required()
                    ->disabled() // Deshabilita el campo
                    ->dehydrated(true) // Esta línea es crucial: asegura que el valor se envíe al servidor aunque esté deshabilitado
                    ->maxLength(100),

                // Campo para mostrar el DNI del empleado (deshabilitado)
                TextInput::make('dni_empleado')
                    ->label('DNI del Empleado')
                    ->disabled() // Deshabilita el campo
                    ->hiddenOn('edit') // Oculta este campo en el formulario de edición
                    ->dehydrated(false), // No guarda este campo en la base de datos

                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->required()
                    ->dehydrateStateUsing(fn($state) => Hash::make($state)), // Encripta la contraseña

                // Using Select Component
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),

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

                TextColumn::make('empleado.nombre_completo')
                    ->label('Empleado')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Correo Electrónico')
                    ->sortable()
                    ->searchable(),


                TextColumn::make('created_at')
                    ->label('Creado')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->sortable()
                    ->searchable(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
