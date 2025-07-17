<?php

namespace App\Filament\Resources\Proforma\ProformaResource\Pages;

use App\Filament\Resources\Proforma\ProformaResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateProforma extends CreateRecord
{
    protected static string $resource = ProformaResource::class;

    protected function afterCreate(): void
    {
        $proforma = $this->record;

        if ($proforma->prospecto) {
            $proforma->prospecto->update([
                'tipo_gestion_id' => 5,
            ]);
        }
        Notification::make()
            ->title('Proforma creada exitosamente')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return ProformaResource::getUrl('index');
    }

    protected function getFormMaxWidth(): string|null
    {
        return '7x1'; 
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = 1; 
        $data['updated_by'] = 1; 

        return $data;
    }

}
