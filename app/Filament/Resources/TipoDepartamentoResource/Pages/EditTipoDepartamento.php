<?php

namespace App\Filament\Resources\TipoDepartamentoResource\Pages;

use App\Filament\Resources\TipoDepartamentoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use App\Models\TipoDepartamento;

class EditTipoDepartamento extends EditRecord
{
    protected static string $resource = TipoDepartamentoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (TipoDepartamento $record) {
                    if ($record->departamentos()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('No se puede eliminar')
                            ->body('Este tipo tiene departamentos asociados')
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
            ->title('Tipo actualizado')
            ->body('El tipo de departamento ha sido modificado correctamente.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}