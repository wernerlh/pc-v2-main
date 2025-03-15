<?php

namespace App\Filament\Resources\TransaccionesJuegoResource\Pages;

use App\Filament\Resources\TransaccionesJuegoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaccionesJuegos extends ListRecords
{
    protected static string $resource = TransaccionesJuegoResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
