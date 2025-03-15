<?php

namespace App\Filament\Resources\JuegosOnlineResource\Pages;

use App\Filament\Resources\JuegosOnlineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJuegosOnline extends EditRecord
{
    protected static string $resource = JuegosOnlineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
