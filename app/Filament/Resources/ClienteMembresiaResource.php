<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteMembresiaResource\Pages;
use App\Models\ClienteMembresia;
use App\Models\Membresia;
use App\Models\UserCliente;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClienteMembresiaResource extends Resource
{
    protected static ?string $model = ClienteMembresia::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Clientes Membresías';
    protected static ?string $modelLabel = 'Cliente Membresía';
    protected static ?string $pluralModelLabel = 'Clientes Membresías';
    protected static ?string $navigationGroup = 'Gestión de Membresías';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('cliente_id')
                    ->label('Cliente')
                    ->options(UserCliente::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('membresia_id')
                    ->label('Membresía')
                    ->options(Membresia::all()->pluck('nombre', 'id'))
                    ->searchable()
                    ->required(),
                DatePicker::make('fecha_inicio')
                    ->label('Fecha de Inicio')
                    ->required(),
                DatePicker::make('fecha_vencimiento')
                    ->label('Fecha de Vencimiento'),
                Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'activa' => 'Activa',
                        'inactiva' => 'Inactiva',
                        'vencida' => 'Vencida',
                        'suspendida' => 'Suspendida',
                    ])
                    ->default('activa')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cliente.name')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('membresia.nombre')
                    ->label('Membresía')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('fecha_inicio')
                    ->label('Fecha de Inicio')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('fecha_vencimiento')
                    ->label('Fecha de Vencimiento')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'activa' => 'success',
                        'inactiva' => 'danger',
                        'vencida' => 'warning',
                        'suspendida' => 'info',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'activa' => 'Activa',
                        'inactiva' => 'Inactiva',
                        'vencida' => 'Vencida',
                        'suspendida' => 'Suspendida',
                    ]),
                SelectFilter::make('cliente_id')
                    ->label('Cliente')
                    ->options(UserCliente::all()->pluck('name', 'id')),
                SelectFilter::make('membresia_id')
                    ->label('Membresia')
                    ->options(Membresia::all()->pluck('nombre', 'id')),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClienteMembresias::route('/'),
            'create' => Pages\CreateClienteMembresia::route('/create'),
            'edit' => Pages\EditClienteMembresia::route('/{record}/edit'),
        ];
    }
}
