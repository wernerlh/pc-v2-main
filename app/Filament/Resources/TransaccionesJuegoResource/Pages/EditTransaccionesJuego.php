<?php

namespace App\Filament\Resources\TransaccionesJuegoResource\Pages;

use App\Filament\Resources\TransaccionesJuegoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaccionesJuego extends EditRecord
{
    protected static string $resource = TransaccionesJuegoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
