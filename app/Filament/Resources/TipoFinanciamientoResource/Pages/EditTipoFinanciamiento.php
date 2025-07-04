<?php

namespace App\Filament\Resources\TipoFinanciamientoResource\Pages;

use App\Filament\Resources\TipoFinanciamientoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use App\Models\TipoFinanciamiento;

class EditTipoFinanciamiento extends EditRecord
{
    protected static string $resource = TipoFinanciamientoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (TipoFinanciamiento $record) {
                    // Aquí puedes agregar tu lógica de validación antes de eliminar
                    // Si hay condiciones a verificar, como que no haya registros asociados, usa esa validación
                    if ($record->some_related_model()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('No se puede eliminar')
                            ->body('Este tipo de financiamiento tiene registros asociados')
                            ->persistent()
                            ->send();

                        return false; // Cancela la eliminación si la condición se cumple
                    }
                }),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Tipo de Financiamiento actualizado')
            ->body('El tipo de financiamiento ha sido modificado correctamente.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
