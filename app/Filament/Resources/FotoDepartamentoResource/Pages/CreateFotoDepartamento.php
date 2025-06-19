<?php

namespace App\Filament\Resources\FotoDepartamentoResource\Pages;

use App\Filament\Resources\FotoDepartamentoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class CreateFotoDepartamento extends CreateRecord
{
    protected static string $resource = FotoDepartamentoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Foto registrada')
            ->body('La foto del departamento ha sido cargada exitosamente.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validación adicional si es necesaria
        return $data;
    }
}