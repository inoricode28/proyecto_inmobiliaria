<?php

namespace App\Filament\Resources\VendedorResource\Pages;

use App\Filament\Resources\VendedorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use App\Models\Vendedor;

class EditVendedor extends EditRecord
{
    protected static string $resource = VendedorResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (Vendedor $record) {
                    // Add any validation before deletion if needed
                    // Example: Prevent deletion if vendedor has related records
                    /*
                    if ($record->someRelation()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('No se puede eliminar')
                            ->body('Este vendedor tiene registros asociados')
                            ->persistent()
                            ->send();
                        return false;
                    }
                    */
                }),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Vendedor actualizado')
            ->body('El vendedor ha sido modificado correctamente.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
