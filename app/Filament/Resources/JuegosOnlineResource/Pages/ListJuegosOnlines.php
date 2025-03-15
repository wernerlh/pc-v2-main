<?php

namespace App\Filament\Resources\JuegosOnlineResource\Pages;

use App\Filament\Resources\JuegosOnlineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJuegosOnlines extends ListRecords
{
    protected static string $resource = JuegosOnlineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
