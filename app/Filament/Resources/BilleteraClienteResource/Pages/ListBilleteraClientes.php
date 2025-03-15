<?php

namespace App\Filament\Resources\BilleteraClienteResource\Pages;

use App\Filament\Resources\BilleteraClienteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBilleteraClientes extends ListRecords
{
    protected static string $resource = BilleteraClienteResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
