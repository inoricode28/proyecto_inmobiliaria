<?php

namespace App\Filament\Resources\EntregaResource\Pages;

use App\Filament\Resources\EntregaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\EstadoDepartamento;
use Filament\Notifications\Notification;

class EditEntrega extends EditRecord
{
    protected static string $resource = EntregaResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        $entrega = $this->record;

        // Cambiar el estado del departamento a 'Entregado' cuando se edite la entrega
        if ($entrega->departamento) {
            $estadoEntregado = EstadoDepartamento::where('nombre', 'Entregado')->first();

            if ($estadoEntregado) {
                $entrega->departamento->update([
                    'estado_departamento_id' => $estadoEntregado->id
                ]);

                Notification::make()
                    ->title('Entrega registrada')
                    //->body('El departamento ha sido marcado como Entregado')
                    ->success()
                    ->send();
            }
        }
    }
}