<?php

namespace App\Filament\Resources\Separaciones\Pages;

use App\Filament\Resources\Separaciones\SeparacionResource;
use Filament\Resources\Pages\EditRecord;

class EditSeparacion extends EditRecord
{
    protected static string $resource = SeparacionResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Puedes agregar lógica para actualizar campos antes de guardar
        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Separación actualizada exitosamente';
    }
}