<?php

namespace App\Filament\Resources\AtencionClienteResource\Pages;

use App\Filament\Resources\AtencionClienteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAtencionCliente extends EditRecord
{
    protected static string $resource = AtencionClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
