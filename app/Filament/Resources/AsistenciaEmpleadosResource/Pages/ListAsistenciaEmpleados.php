<?php

namespace App\Filament\Resources\AsistenciaEmpleadosResource\Pages;

use App\Filament\Resources\AsistenciaEmpleadosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAsistenciaEmpleados extends ListRecords
{
    protected static string $resource = AsistenciaEmpleadosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
