<?php

namespace App\Filament\Resources\CategoriasJuegoResource\Pages;

use App\Filament\Resources\CategoriasJuegoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoriasJuegos extends ListRecords
{
    protected static string $resource = CategoriasJuegoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
