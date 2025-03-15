<?php

namespace App\Filament\Resources\UserClienteResource\Pages;

use App\Filament\Resources\UserClienteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserClientes extends ListRecords
{
    protected static string $resource = UserClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
