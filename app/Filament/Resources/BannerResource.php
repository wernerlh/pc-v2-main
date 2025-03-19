<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Banners';

    protected static ?string $pluralModelLabel = 'Banners';

    protected static ?string $modelLabel = 'Banner';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->label('Nombre del Banner')
                    ->required()
                    ->maxLength(255),

                Select::make('icono')
                    ->label('Ícono')
                    ->options([
                        'heroicon-o-megaphone' => 'Megáfono',
                        'heroicon-o-bell' => 'Campana',
                        'heroicon-o-exclamation-triangle' => 'Advertencia',
                        'heroicon-o-information-circle' => 'Información',
                        'heroicon-o-sparkles' => 'Destacado',
                        'heroicon-o-star' => 'Estrella',
                        'heroicon-o-gift' => 'Regalo',
                        'heroicon-o-currency-dollar' => 'Promoción',
                    ])
                    ->default('heroicon-o-megaphone')
                    ->required(),

                Textarea::make('contenido')
                    ->label('Contenido')
                    ->required()
                    ->maxLength(1000)
                    ->columnSpanFull(),

                Select::make('color')
                    ->label('Color')
                    ->options([
                        'primary' => 'Principal (Azul)',
                        'secondary' => 'Secundario (Gris)',
                        'success' => 'Éxito (Verde)',
                        'warning' => 'Advertencia (Amarillo)',
                        'danger' => 'Peligro (Rojo)',
                        'info' => 'Información (Celeste)',
                    ])
                    ->default('primary')
                    ->required(),

                Toggle::make('activo')
                    ->label('Activo')
                    ->default(true),

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

                TextColumn::make('icono')
                    ->label('Ícono')
                    ->icon(fn(string $state): string => $state),

                TextColumn::make('contenido')
                    ->label('Contenido')
                    ->limit(50)
                    ->searchable(),

                TextColumn::make('color')
                    ->label('Color')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'primary' => 'Principal',
                        'secondary' => 'Secundario',
                        'success' => 'Éxito',
                        'warning' => 'Advertencia',
                        'danger' => 'Peligro',
                        'info' => 'Información',
                        default => $state,
                    })
                    ->color(fn(string $state): string => $state),

                IconColumn::make('activo')
                    ->label('Banner activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('gray'),


            ])
            ->defaultSort('posicion')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

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
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
