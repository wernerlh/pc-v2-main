<?php

namespace App\Filament\Resources\TransaccionesCasinoPResource\Pages;

use App\Filament\Resources\TransaccionesCasinoPResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaccionesCasinoPS extends ListRecords
{
    protected static string $resource = TransaccionesCasinoPResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
