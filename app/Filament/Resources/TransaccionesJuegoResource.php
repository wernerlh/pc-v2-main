<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaccionesJuegoResource\Pages;
use App\Models\JuegosOnline;
use App\Models\TransaccionesJuego;
use App\Models\UserCliente;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TransaccionesJuegoResource extends Resource
{
    protected static ?string $model = TransaccionesJuego::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Transacciones de Juego';
    protected static ?string $modelLabel = 'Transacción de Juego';
    protected static ?string $pluralModelLabel = 'Transacciones de Juego';
    protected static ?string $navigationGroup = 'Gestión Financiera';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'usuariocasino/transacciones-juego';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('cliente_id')
                    ->label('Cliente')
                    ->options(UserCliente::all()->pluck('nombre_completo', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('juego_id')
                    ->label('Juego')
                    ->options(JuegosOnline::all()->pluck('nombre', 'id'))
                    ->searchable()
                    ->required(),
                DateTimePicker::make('fecha_hora')
                    ->label('Fecha y Hora')
                    ->required(),
                TextInput::make('monto_apostado')
                    ->label('Monto Apostado')
                    ->numeric()
                    ->required(),
                TextInput::make('monto_ganado')
                    ->label('Monto Ganado')
                    ->numeric()
                    ->default(0),
                Select::make('tipo_transaccion')
                    ->label('Tipo de Transacción')
                    ->options([
                        'apuesta' => 'Apuesta',
                        'ganancia' => 'Ganancia',
                        'bonificación' => 'Bonificación',
                        'freespin' => 'Free Spin',
                    ])
                    ->required(),
                TextInput::make('balance_anterior')
                    ->label('Balance Anterior')
                    ->numeric()
                    ->required(),
                TextInput::make('balance_posterior')
                    ->label('Balance Posterior')
                    ->numeric()
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
                TextColumn::make('juego.nombre')
                    ->label('Juego')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('fecha_hora')
                    ->label('Fecha y Hora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('monto_apostado')
                    ->label('Monto Apostado')
                    ->sortable(),
                TextColumn::make('monto_ganado')
                    ->label('Monto Ganado')
                    ->sortable(),
                TextColumn::make('tipo_transaccion')
                    ->label('Tipo de Transacción')
                    ->sortable(),
                TextColumn::make('balance_anterior')
                    ->label('Balance Anterior'),
                TextColumn::make('balance_posterior')
                    ->label('Balance Posterior'),

            ])
            ->filters([
                SelectFilter::make('tipo_transaccion')
                    ->label('Tipo de Transacción')
                    ->options([
                        'apuesta' => 'Apuesta',
                        'ganancia' => 'Ganancia',
                        'bonificación' => 'Bonificación',
                        'freespin' => 'Free Spin',
                    ])
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
            'index' => Pages\ListTransaccionesJuegos::route('/'),
        ];
    }
}
