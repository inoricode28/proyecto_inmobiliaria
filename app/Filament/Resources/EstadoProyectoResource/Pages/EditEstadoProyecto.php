<?php

namespace App\Filament\Resources\EstadoProyectoResource\Pages;

use App\Filament\Resources\EstadoProyectoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use App\Models\EstadoProyecto;

class EditEstadoProyecto extends EditRecord
{
    protected static string $resource = EstadoProyectoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (EstadoProyecto $record) {
                    if ($record->proyectos()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('No se puede eliminar')
                            ->body('Este estado tiene proyectos asociados')
                            ->persistent()
                            ->send();
                        
                        // Detener la eliminación
                        return false;
                    }
                }),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Estado actualizado')
            ->body('El estado de proyecto ha sido modificado correctamente.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Validación adicional si es necesaria
        return $data;
    }
}