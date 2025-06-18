<?php

namespace App\Filament\Resources\EstadoDepartamentoResource\Pages;

use App\Filament\Resources\EstadoDepartamentoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateEstadoDepartamento extends CreateRecord
{
    protected static string $resource = EstadoDepartamentoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Estado creado')
            ->body('El estado de departamento ha sido registrado correctamente.');
    }
}