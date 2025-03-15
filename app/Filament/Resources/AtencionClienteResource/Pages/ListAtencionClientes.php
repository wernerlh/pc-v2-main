<?php

namespace App\Filament\Resources\AtencionClienteResource\Pages;

use App\Filament\Resources\AtencionClienteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAtencionClientes extends ListRecords
{
    protected static string $resource = AtencionClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
