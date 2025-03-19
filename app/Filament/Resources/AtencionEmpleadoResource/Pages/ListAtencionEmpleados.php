<?php

namespace App\Filament\Resources\AtencionEmpleadoResource\Pages;

use App\Filament\Resources\AtencionEmpleadoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAtencionEmpleados extends ListRecords
{
    protected static string $resource = AtencionEmpleadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
