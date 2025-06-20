<?php

namespace App\Filament\Resources\ConsultaStockResource\Pages;

use App\Filament\Resources\ConsultaStockResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStockPorPiso extends ViewRecord
{
    protected static string $resource = ConsultaStockResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}