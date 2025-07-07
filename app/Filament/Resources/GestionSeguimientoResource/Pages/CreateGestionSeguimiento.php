<?php

namespace App\Filament\Resources\GestionSeguimientoResource\Pages;

use App\Filament\Resources\GestionSeguimientoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateGestionSeguimiento extends CreateRecord
{
    protected static string $resource = GestionSeguimientoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Prospecto creado')
            ->body('El prospecto ha sido registrado correctamente.');
    }
}