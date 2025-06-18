<?php

namespace App\Filament\Resources\EdificioResource\Pages;

use App\Filament\Resources\EdificioResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use App\Models\Edificio;

class EditEdificio extends EditRecord
{
    protected static string $resource = EdificioResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (Edificio $record) {
                    if ($record->departamentos()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('No se puede eliminar')
                            ->body('Este edificio tiene departamentos asociados')
                            ->persistent()
                            ->send();
                        
                        return false;
                    }
                }),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Edificio actualizado')
            ->body('El edificio ha sido modificado correctamente.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}