<?php

namespace App\Filament\Resources\EmpresaResource\Pages;

use App\Filament\Resources\EmpresaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use App\Models\Empresa;

class EditEmpresa extends EditRecord
{
    protected static string $resource = EmpresaResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (Empresa $record) {
                    if ($record->proyectos()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('No se puede eliminar')
                            ->body('Esta empresa tiene proyectos asociados')
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
            ->title('Empresa actualizada')
            ->body('La empresa ha sido modificada correctamente.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}