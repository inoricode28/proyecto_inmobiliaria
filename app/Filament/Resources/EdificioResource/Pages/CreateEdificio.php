<?php

namespace App\Filament\Resources\EdificioResource\Pages;

use App\Filament\Resources\EdificioResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateEdificio extends CreateRecord
{
    protected static string $resource = EdificioResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Edificio creado')
            ->body('El edificio ha sido registrado correctamente.');
    }
}