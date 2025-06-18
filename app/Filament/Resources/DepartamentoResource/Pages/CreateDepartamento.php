<?php

namespace App\Filament\Resources\DepartamentoResource\Pages;

use App\Filament\Resources\DepartamentoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateDepartamento extends CreateRecord
{
    protected static string $resource = DepartamentoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Departamento creado')
            ->body('El departamento ha sido registrado correctamente.');
    }
}