<?php

namespace App\Filament\Resources\CategoriasJuegoResource\Pages;

use App\Filament\Resources\CategoriasJuegoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoriasJuego extends EditRecord
{
    protected static string $resource = CategoriasJuegoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
