<?php

namespace App\Filament\Resources\UserClienteResource\Pages;

use App\Filament\Resources\UserClienteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserCliente extends EditRecord
{
    protected static string $resource = UserClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
