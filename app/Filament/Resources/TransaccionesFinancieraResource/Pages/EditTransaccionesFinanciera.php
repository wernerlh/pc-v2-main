<?php

namespace App\Filament\Resources\TransaccionesFinancieraResource\Pages;

use App\Filament\Resources\TransaccionesFinancieraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaccionesFinanciera extends EditRecord
{
    protected static string $resource = TransaccionesFinancieraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
}
