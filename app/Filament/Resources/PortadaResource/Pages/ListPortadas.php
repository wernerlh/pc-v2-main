<?php

namespace App\Filament\Resources\PortadaResource\Pages;

use App\Filament\Resources\PortadaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPortadas extends ListRecords
{
    protected static string $resource = PortadaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
