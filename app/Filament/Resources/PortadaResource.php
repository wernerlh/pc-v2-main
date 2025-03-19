<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PortadaResource\Pages;
use App\Models\Portada;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PortadaResource extends Resource
{
    protected static ?string $model = Portada::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Portadas';

    protected static ?string $pluralModelLabel = 'Portadas';

    protected static ?string $modelLabel = 'Portada';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('titulo')
                    ->label('Título')
                    ->required()
                    ->unique()
                    ->maxLength(255),

                TextInput::make('imagen_url')
                    ->label('URL de la imagen')
                    ->required()
                    ->unique()
                    ->url()
                    ->maxLength(255)
                    ->helperText('Ingrese la URL completa de la imagen'),

                Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'activo' => 'Activo',
                        'inactivo' => 'Inactivo',
                    ])
                    ->default('activo')
                    ->required(),

                TextInput::make('orden')
                    ->label('Orden')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->unique( )
                    ->helperText('Los valores menores aparecen primero'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titulo')
                    ->label('Título')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('imagen_url')
                    ->label('URL de Imagen')
                    ->limit(30)
                    ->searchable(),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'activo' => 'Activo',
                        'inactivo' => 'Inactivo',
                        default => $state,
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'activo' => 'success',
                        'inactivo' => 'danger',
                        default => 'gray',
                    }),

                IconColumn::make('isActive')
                    ->label('Estado actual')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn(Portada $record): bool => $record->isActive()),

                TextColumn::make('orden')
                    ->label('Orden')
                    ->sortable(),
                    
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('orden')
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'activo' => 'Activo',
                        'inactivo' => 'Inactivo',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activar')
                        ->icon('heroicon-o-check')
                        ->action(fn(Builder $query) => $query->update(['estado' => 'activo'])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Desactivar')
                        ->icon('heroicon-o-x-mark')
                        ->action(fn(Builder $query) => $query->update(['estado' => 'inactivo'])),
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
            'index' => Pages\ListPortadas::route('/'),
            'create' => Pages\CreatePortada::route('/create'),
            'edit' => Pages\EditPortada::route('/{record}/edit'),
        ];
    }
}