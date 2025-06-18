<?php

namespace App\Filament\Resources\ProyectoResource\Pages;

use App\Filament\Resources\ProyectoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use App\Models\Proyecto;

class EditProyecto extends EditRecord
{
    protected static string $resource = ProyectoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (Proyecto $record) {
                    if ($record->edificios()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('No se puede eliminar')
                            ->body('Este proyecto tiene edificios asociados')
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
            ->title('Proyecto actualizado')
            ->body('El proyecto ha sido modificado correctamente.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}