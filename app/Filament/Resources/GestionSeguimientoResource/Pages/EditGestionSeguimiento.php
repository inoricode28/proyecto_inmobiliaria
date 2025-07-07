<?php

namespace App\Filament\Resources\GestionSeguimientoResource\Pages;

use App\Filament\Resources\GestionSeguimientoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditGestionSeguimiento extends EditRecord
{
    protected static string $resource = GestionSeguimientoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ViewAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Prospecto actualizado')
            ->body('El prospecto ha sido modificado correctamente.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}