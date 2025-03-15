<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MembresiaResource\Pages;
use App\Models\Membresia;
use Filament\Forms\Components\Decimal;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MembresiaResource extends Resource
{
    protected static ?string $model = Membresia::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Membresías';
    protected static ?string $modelLabel = 'Membresía';
    protected static ?string $pluralModelLabel = 'Membresías';
    protected static ?string $navigationGroup = 'Gestión de Membresías';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(100),
                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->maxLength(500)
                    ->columnSpanFull(),
                Textarea::make('beneficios')
                    ->label('Beneficios')
                    ->columnSpanFull(),
                TextInput::make('descuento_porcentaje')
                    ->label('Descuento (%)')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(100),
                TextInput::make('precio')
                    ->label('Precio')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('beneficios')
                    ->label('Beneficios')
                    ->wrap(),
                TextColumn::make('descuento_porcentaje')
                    ->label('Descuento (%)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('precio')
                    ->label('Precio')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                
            ])
            ->filters([])
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
            'index' => Pages\ListMembresias::route('/'),
            'create' => Pages\CreateMembresia::route('/create'),
            'edit' => Pages\EditMembresia::route('/{record}/edit'),
        ];
    }
}
