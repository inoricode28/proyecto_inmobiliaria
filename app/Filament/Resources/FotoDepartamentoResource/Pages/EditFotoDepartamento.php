<?php

namespace App\Filament\Resources\FotoDepartamentoResource\Pages;

use App\Filament\Resources\FotoDepartamentoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class EditFotoDepartamento extends EditRecord
{
    protected static string $resource = FotoDepartamentoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    Storage::disk('public')->delete($record->imagen);
                }),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Foto actualizada')
            ->body('La foto del departamento ha sido modificada correctamente.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Lógica adicional antes de guardar si es necesaria
        return $data;
    }
}