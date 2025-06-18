<?php

namespace App\Filament\Resources\EstadoProyectoResource\Pages;

use App\Filament\Resources\EstadoProyectoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateEstadoProyecto extends CreateRecord
{
    protected static string $resource = EstadoProyectoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Estado creado')
            ->body('El estado de proyecto ha sido registrado correctamente.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validación adicional si es necesaria
        return $data;
    }
}