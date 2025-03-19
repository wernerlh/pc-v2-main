<?php

namespace App\Filament\Resources\PortadaResource\Pages;

use App\Filament\Resources\PortadaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPortada extends EditRecord
{
    protected static string $resource = PortadaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
