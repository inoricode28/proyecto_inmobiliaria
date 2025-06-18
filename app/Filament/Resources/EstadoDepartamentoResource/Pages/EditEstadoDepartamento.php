<?php

namespace App\Filament\Resources\EstadoDepartamentoResource\Pages;

use App\Filament\Resources\EstadoDepartamentoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use App\Models\EstadoDepartamento;

class EditEstadoDepartamento extends EditRecord
{
    protected static string $resource = EstadoDepartamentoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (EstadoDepartamento $record) {
                    if ($record->departamentos()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('No se puede eliminar')
                            ->body('Este estado tiene departamentos asociados')
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
            ->title('Estado actualizado')
            ->body('El estado de departamento ha sido modificado correctamente.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}