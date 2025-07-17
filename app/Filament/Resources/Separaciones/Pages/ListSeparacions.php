<?php

namespace App\Filament\Resources\Separaciones\Pages;

use App\Filament\Resources\Separaciones\SeparacionResource;
use Filament\Resources\Pages\ListRecords;

class ListSeparacions extends ListRecords
{
    protected static string $resource = SeparacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Puedes agregar botones de acción como crear aquí
            // Actions\CreateAction::make(), 
        ];
    }
}
