<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaccionesCasinoPResource\Pages;
use App\Filament\Resources\TransaccionesCasinoPResource\RelationManagers;
use App\Models\TransaccionesCasinoP;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use App\Models\User;
use App\Models\Sucursales;
use Illuminate\Database\Eloquent\Builder;
use App\Models\UserCliente;
use App\Models\Empleados;



class TransaccionesCasinoPResource extends Resource
{
    protected static ?string $model = TransaccionesCasinoP::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Gestión Financiera';

    protected static ?string $navigationLabel = 'Transacciones en el Casino';

    protected static ?string $pluralModelLabel = 'Transacciones en el Casino';

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
                    ->label('Empleado Asignado')
                    ->options(Empleados::all()->pluck('nombre_completo', 'empleado_id'))
                    ->searchable()
                    ->required(),

                Select::make('sucursal_id')
                    ->label('Sucursal')
                    ->options(Sucursales::pluck('nombre', 'id'))
                    ->searchable()
                    ->required(),

                DateTimePicker::make('fecha')
                    ->label('Fecha y Hora')
                    ->required()
                    ->default(now()),

                Select::make('tipo')
                    ->label('Tipo de Transacción')
                    ->options([
                        'deposito' => 'Depósito',
                        'retiro' => 'Retiro',
                    ])
                    ->required(),

                TextInput::make('monto')
                    ->label('Monto')
                    ->numeric()
                    ->minValue(0.01)
                    ->required(),

                Textarea::make('observacion')
                    ->label('Observación')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('cliente.nombre_completo')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('empleado.nombre_completo')
                    ->label('Empleado')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('sucursal.nombre')
                    ->label('Sucursal')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->formatStateUsing(fn(string $state): string => $state === 'deposito' ? 'Depósito' : 'Retiro')
                    ->badge()
                    ->color(fn(string $state): string => $state === 'deposito' ? 'success' : 'danger'),

                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('PEN')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'deposito' => 'Depósito',
                        'retiro' => 'Retiro',
                    ]),
                Tables\Filters\SelectFilter::make('sucursal_id')
                    ->label('Sucursal')
                    ->options(Sucursales::pluck('nombre', 'id')),
                Tables\Filters\Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('desde'),
                        Forms\Components\DatePicker::make('hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha', '<=', $date),
                            );
                    })
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
            'index' => Pages\ListTransaccionesCasinoPS::route('/'),
            'create' => Pages\CreateTransaccionesCasinoP::route('/create'),
            'edit' => Pages\EditTransaccionesCasinoP::route('/{record}/edit'),
        ];
    }
}
