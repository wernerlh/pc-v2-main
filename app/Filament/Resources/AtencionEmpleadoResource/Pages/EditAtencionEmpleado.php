<?php

namespace App\Filament\Resources\AtencionEmpleadoResource\Pages;

use App\Filament\Resources\AtencionEmpleadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAtencionEmpleado extends EditRecord
{
    protected static string $resource = AtencionEmpleadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
