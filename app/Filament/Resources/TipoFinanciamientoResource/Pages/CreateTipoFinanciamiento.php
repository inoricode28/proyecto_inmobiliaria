<?php

namespace App\Filament\Resources\TipoFinanciamientoResource\Pages;

use App\Filament\Resources\TipoFinanciamientoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateTipoFinanciamiento extends CreateRecord
{
    protected static string $resource = TipoFinanciamientoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Tipo de Financiamiento Creado')
            ->body('El nuevo tipo de financiamiento ha sido registrado correctamente.');
    }
}
