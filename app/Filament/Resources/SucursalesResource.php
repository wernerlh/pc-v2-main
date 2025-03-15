<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SucursalesResource\Pages;
use App\Models\Sucursales;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SucursalesResource extends Resource
{
    protected static ?string $model = Sucursales::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2'; // Icono para el menú de navegación
    protected static ?string $navigationLabel = 'Sucursales'; // Etiqueta del menú
    protected static ?string $modelLabel = 'Sucursales'; // Etiqueta singular
    protected static ?string $pluralModelLabel = 'Sucursales'; // Etiqueta plural
    protected static ?string $navigationGroup = 'Gestión de Empresa'; // Grupo de navegación

    protected static ?int $navigationSort = 1;



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->required()
                    ->maxLength(100)
                    ->unique(),
                TextInput::make('direccion')
                    ->required()
                    ->maxLength(255)
                    ->unique(),
                TextInput::make('telefono')
                    ->required()
                    ->nullable()
                    ->maxLength(15)
                    ->unique(),
                TextInput::make('ciudad')
                
                    ->required()
                    ->maxLength(100),
                TextInput::make('provincia')
                    ->required()
                    ->maxLength(100),
                TextInput::make('codigo_postal')
                    ->required()
                    ->maxLength(20),
                TextInput::make('pais')
                    ->required()
                    ->maxLength(100),
                Select::make('tipo_establecimiento')
                    ->options([
                        'casino' => 'Casino',
                        'hotel' => 'Hotel',
                        'mixto' => 'Mixto',
                    ])
                    ->required(),
                TextInput::make('capacidad')
                    ->numeric()
                    ->required(),
                DatePicker::make('fecha_inauguracion')
                    ->required(),


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
                TextColumn::make('nombre'),
                TextColumn::make('direccion'),
                TextColumn::make('telefono'),
                TextColumn::make('ciudad'),
                TextColumn::make('provincia'),
                TextColumn::make('codigo_postal'),
                TextColumn::make('pais'),
                TextColumn::make('tipo_establecimiento'),
                TextColumn::make('capacidad'),
                TextColumn::make('fecha_inauguracion'),


            ])
            ->filters([
                SelectFilter::make('tipo_establecimiento')
                    ->options([
                        'casino' => 'Casino',
                        'hotel' => 'Hotel',
                        'mixto' => 'Mixto',
                    ]),
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
            'index' => Pages\ListSucursales::route('/'),
            'create' => Pages\CreateSucursales::route('/create'),
            'edit' => Pages\EditSucursales::route('/{record}/edit'),
        ];
    }
}
