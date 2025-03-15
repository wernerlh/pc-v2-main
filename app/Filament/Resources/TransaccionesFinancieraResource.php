<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaccionesFinancieraResource\Pages;
use App\Models\Empleados;
use App\Models\TransaccionesFinanciera;
use App\Models\UserCliente;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;

class TransaccionesFinancieraResource extends Resource
{
    protected static ?string $model = TransaccionesFinanciera::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Transacciones Financieras';
    protected static ?string $modelLabel = 'Transacción Financiera';
    protected static ?string $pluralModelLabel = 'Transacciones Financieras';
    protected static ?string $navigationGroup = 'Gestión Financiera';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'usuariocasino/transacciones-financieras';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('cliente_id')
                    ->label('Cliente')
                    ->options(UserCliente::all()->pluck('nombre_completo', 'id'))
                    ->searchable()
                    ->disabled()
                    ->required(),
                TextInput::make('monto')
                    ->label('Monto')
                    ->numeric()
                    ->disabled()
                    ->required(),
                Select::make('tipo')
                    ->label('Tipo')
                    ->disabled()
                    ->options([
                        'deposito' => 'Depósito',
                        'retiro' => 'Retiro',
                    ])
                    ->required(),
                Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'completada' => 'Completada',
                        'rechazada' => 'Rechazada',
                    ])
                    ->default('pendiente')
                    ->required()
                    ->live(),
                TextInput::make('numero_cuenta_bancaria')
                    ->disabled()
                    ->label('Número de Cuenta Bancaria'),
                TextInput::make('banco')
                    ->disabled()
                    ->label('Banco'),
                TextInput::make('titular_cuenta')
                    ->disabled()
                    ->label('Titular de la Cuenta'),
                TextInput::make('referencia_transferencia')
                    ->disabled()
                    ->label('Referencia de Transferencia'),
                DateTimePicker::make('fecha_solicitud')
                    ->label('Fecha de Solicitud')
                    ->disabled()
                    ->required(),
                DateTimePicker::make('fecha_procesamiento')
                    ->label('Fecha de Procesamiento'),
                Textarea::make('motivo_rechazo')
                    ->label('Motivo de Rechazo')
                    ->visible(fn (Get $get): bool => $get('estado') === 'rechazada')
                    ->columnSpanFull(),
                Select::make('revisado_por')
                    ->label('Revisado Por')
                    ->options(Empleados::all()->pluck('nombre_completo', 'empleado_id'))
                    ->nullable(),
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
                TextColumn::make('monto')
                    ->label('Monto')
                    ->sortable(),
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->sortable(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'completada' => 'success',
                        'rechazada' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('numero_cuenta_bancaria')
                    ->label('Número de Cuenta Bancaria'),
                TextColumn::make('banco')
                    ->label('Banco'),
                TextColumn::make('titular_cuenta')
                    ->label('Titular de la Cuenta'),
                TextColumn::make('referencia_transferencia')
                    ->label('Referencia de Transferencia'),
                TextColumn::make('fecha_solicitud')
                    ->label('Fecha de Solicitud')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('fecha_procesamiento')
                    ->label('Fecha de Procesamiento')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('empleado.nombre_completo')
                    ->label('Revisado Por')
                    ->sortable()
                    ->searchable(),

            ])
            ->filters([
                SelectFilter::make('tipo')
                    ->options([
                        'deposito' => 'Depósito',
                        'retiro' => 'Retiro',
                        'ajuste' => 'Ajuste',
                        'bono' => 'Bono',
                        'cashback' => 'Cashback',
                    ]),
                SelectFilter::make('estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'completada' => 'Completada',
                        'rechazada' => 'Rechazada',
                        'cancelada' => 'Cancelada',
                        'en_revision' => 'En Revisión',
                    ]),
            ])
            ->actions([
                EditAction::make()
                ->visible(fn ($record) => $record->estado === 'pendiente'),

            ])
            ->bulkActions([]);
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
            'index' => Pages\ListTransaccionesFinancieras::route('/'),
        ];
    }
}
