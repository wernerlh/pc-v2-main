<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BilleteraClienteResource\Pages;
use App\Models\BilleteraCliente;
use App\Models\UserCliente;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BilleteraClienteResource extends Resource
{
    protected static ?string $model = BilleteraCliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';
    protected static ?string $navigationLabel = 'Billeteras de Clientes';
    protected static ?string $modelLabel = 'Billetera de Cliente';
    protected static ?string $pluralModelLabel = 'Billeteras de Clientes';
    protected static ?string $navigationGroup = 'Gestión de Clientes';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('cliente_id')
                    ->label('Cliente')
                    ->options(UserCliente::all()->pluck('nombre_completo', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('balance_real')
                    ->label('Balance Real')
                    ->numeric()
                    ->required(),
                TextInput::make('balance_rechazadas')
                    ->label('Balance Rechazadas')
                    ->numeric()
                    ->required(),
                TextInput::make('balance_pendiente')
                    ->label('Balance Pendiente')
                    ->numeric()
                    ->required(),
                TextInput::make('total_depositado')
                    ->label('Total Depositado')
                    ->numeric()
                    ->required(),
                TextInput::make('total_retirado')
                    ->label('Total Retirado')
                    ->numeric()
                    ->required(),
                TextInput::make('total_ganado')
                    ->label('Total Ganado')
                    ->numeric()
                    ->required(),
                TextInput::make('total_apostado')
                    ->label('Total Apostado')
                    ->numeric()
                    ->required(),
                TextInput::make('moneda')
                    ->label('Moneda')
                    ->default('PEN')
                    ->required(),
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
                TextColumn::make('balance_real')
                    ->label('Balance Real')
                    ->sortable(),
                TextColumn::make('balance_rechazadas')
                    ->label('Balance Rechazadas')
                    ->sortable(),
                TextColumn::make('balance_pendiente')
                    ->label('Balance Pendiente')
                    ->sortable(),
                TextColumn::make('total_depositado')
                    ->label('Total Depositado'),
                TextColumn::make('total_retirado')
                    ->label('Total Retirado'),
                TextColumn::make('total_ganado')
                    ->label('Total Ganado'),
                TextColumn::make('total_apostado')
                    ->label('Total Apostado'),
                TextColumn::make('moneda')
                    ->label('Moneda'),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([

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
            'index' => Pages\ListBilleteraClientes::route('/'),
        ];
    }
}
