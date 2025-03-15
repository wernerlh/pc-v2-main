<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoriasJuegoResource\Pages;
use App\Models\CategoriasJuego;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CategoriasJuegoResource extends Resource
{
    protected static ?string $model = CategoriasJuego::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'Categorías de Juegos';
    protected static ?string $modelLabel = 'Categoría de Juego';
    protected static ?string $pluralModelLabel = 'Categorías de Juegos';
    protected static ?string $navigationGroup = 'Gestión de Juegos';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->label('Nombre de la categoría')
                    ->required()
                    ->maxLength(100),
                Textarea::make('descripcion')
                    ->label('Descripción')
                    ->maxLength(500)
                    ->columnSpanFull(),
                TextInput::make('imagen_url')
                    ->label('URL de la Imagen')
                    ->url()
                    ->columnSpanFull(),
                TextInput::make('orden')
                    ->label('Orden de Visualización')
                    ->numeric()
                    ->default(0),
                Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'activo' => 'Activo',
                        'inactivo' => 'Inactivo',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('imagen_url')
                    ->label('URL de la Imagen')
                    ->wrap(),
                TextColumn::make('orden')
                    ->label('Orden'),
                TextColumn::make('estado')
                    ->label('Estado'),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'activo' => 'Activo',
                        'inactivo' => 'Inactivo',
                    ])
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
            'index' => Pages\ListCategoriasJuegos::route('/'),
            'create' => Pages\CreateCategoriasJuego::route('/create'),
            'edit' => Pages\EditCategoriasJuego::route('/{record}/edit'),
        ];
    }
}
