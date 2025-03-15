<?php

namespace App\Filament\Resources\TransaccionesFinancieraResource\Pages;

use App\Filament\Resources\TransaccionesFinancieraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaccionesFinancieras extends ListRecords
{
    protected static string $resource = TransaccionesFinancieraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
