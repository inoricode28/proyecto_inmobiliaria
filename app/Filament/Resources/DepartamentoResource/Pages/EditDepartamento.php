<?php

namespace App\Filament\Resources\DepartamentoResource\Pages;

use App\Filament\Resources\DepartamentoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditDepartamento extends EditRecord
{
    protected static string $resource = DepartamentoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Departamento actualizado')
            ->body('El departamento ha sido modificado correctamente.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}