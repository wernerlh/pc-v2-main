<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmpleadosResource\Pages;
use App\Filament\Resources\EmpleadosResource\RelationManagers;
use App\Models\Empleados;
use App\Models\Sucursales;
use App\Models\Departamentos;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\SelectColumn;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmpleadosResource extends Resource
{
    protected static ?string $model = Empleados::class;

    protected static ?string $navigationIcon = 'heroicon-o-users'; // Icono para el menú de navegación
    protected static ?string $navigationLabel = 'Empleados'; // Etiqueta del menú
    protected static ?string $modelLabel = 'Empleado'; // Etiqueta singular
    protected static ?string $pluralModelLabel = 'Empleados'; // Etiqueta plural
    protected static ?string $navigationGroup = 'Gestión de Empresa'; // Grupo de navegación

    protected static ?int $navigationSort = 3;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre_completo')
                    ->label('Nombre Completo')
                    ->required()
                    ->maxLength(100),
                TextInput::make('documento_identidad')
                    ->label('Documento de Identidad')
                    ->required()
                    ->unique()
                    ->maxLength(20),
                TextInput::make('correo')
                    ->label('Correo Electrónico')
                    ->required()
                    ->email()
                    ->unique()
                    ->maxLength(100),
                TextInput::make('telefono')
                    ->label('Teléfono')
                    ->required()
                    ->nullable()
                    ->maxLength(15),
                TextInput::make('cargo')
                    ->label('Cargo de trabajo')

                    ->required()
                    ->maxLength(100),
                DatePicker::make('fecha_contratacion')
                    ->label('Fecha de Contratación')
                    ->required(),
                DatePicker::make('fecha_nacimiento')
                    ->label('Fecha de Nacimiento')
                    ->required(),
                Select::make('estado')
                    ->label('Estado de trabajo')
                    ->options([
                        'ACTIVO' => 'Activo',
                        'INACTIVO' => 'Inactivo',
                        'VACACIONES' => 'Vacaciones',
                        'LICENCIA' => 'Licencia',
                        'DESPEDIDO' => 'Despedido',
                    ])
                    ->required(),
                TextInput::make('salario_base')
                    ->label('Salario Base')
                    ->numeric()
                    ->required(),
                Select::make('sucursal_id')
                    ->label('Sucursal')
                    ->relationship('sucursal', 'nombre')
                    ->required(),
                Select::make('supervisor_id')
                    ->label('Supervisor')
                    ->relationship('supervisor', 'nombre_completo')
                    ->nullable(),
                Select::make('departamento_id')
                    ->label('Departamento')
                    ->relationship('departamento', 'nombre')
                    ->nullable(),

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

                TextColumn::make('nombre_completo')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('documento_identidad')
                    ->label('Documento de Identidad')
                    ->searchable(),

                TextColumn::make('correo')
                    ->label('Correo Electrónico')
                    ->searchable(),

                TextColumn::make('cargo')
                    ->label('Cargo')
                    ->sortable(),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->sortable(),

                TextColumn::make('salario_base')
                    ->label('Salario Base')
                    ->money('S/.') // Formato de moneda
                    ->sortable(),

                TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->sortable(),

                TextColumn::make('supervisor.nombre_completo')
                    ->label('Supervisor')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'ACTIVO' => 'Activo',
                        'INACTIVO' => 'Inactivo',
                        'VACACIONES' => 'Vacaciones',
                        'LICENCIA' => 'Licencia',
                        'DESPEDIDO' => 'Despedido',
                    ]),
                Tables\Filters\SelectFilter::make('sucursal_id')
                    ->relationship('sucursal', 'nombre')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('supervisor_id')
                    ->relationship('supervisor', 'nombre_completo')
                    ->searchable(),

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
            'index' => Pages\ListEmpleados::route('/'),
            'create' => Pages\CreateEmpleados::route('/create'),
            'edit' => Pages\EditEmpleados::route('/{record}/edit'),
        ];
    }
}
