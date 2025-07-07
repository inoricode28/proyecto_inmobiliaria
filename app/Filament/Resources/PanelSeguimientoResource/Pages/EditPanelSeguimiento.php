<?php

namespace App\Filament\Resources\PanelSeguimientoResource\Pages;

use App\Filament\Resources\PanelSeguimientoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPanelSeguimiento extends EditRecord
{
    protected static string $resource = PanelSeguimientoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
