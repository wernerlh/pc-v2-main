<?php

namespace App\Filament\Resources\TransaccionesCasinoPResource\Pages;

use App\Filament\Resources\TransaccionesCasinoPResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaccionesCasinoP extends EditRecord
{
    protected static string $resource = TransaccionesCasinoPResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
