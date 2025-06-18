<?php

namespace App\Filament\Resources\TipoDepartamentoResource\Pages;

use App\Filament\Resources\TipoDepartamentoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateTipoDepartamento extends CreateRecord
{
    protected static string $resource = TipoDepartamentoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Tipo creado')
            ->body('El tipo de departamento ha sido registrado correctamente.');
    }
}