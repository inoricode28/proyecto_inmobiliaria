<?php

namespace App\Filament\Resources\Separaciones\Pages;

use App\Models\Separacion;
use App\Models\EstadoDepartamento;

use App\Filament\Resources\Separaciones\SeparacionResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateSeparacion extends CreateRecord
{
    protected static string $resource = SeparacionResource::class;

    protected function handleRecordCreation(array $data): \App\Models\Separacion
    {
        $notariaData = $data['notaria_kardex'];
        $cartaData = $data['carta_fianza'];

        unset($data['notaria_kardex'], $data['carta_fianza']);

        $separacion = Separacion::create($data);
        $separacion->notariaKardex()->create($notariaData);
        $separacion->cartaFianza()->create($cartaData);

        return $separacion;
    }

    protected function afterCreate()
    {
        $separacion = $this->record;

        // Cambiar el estado del departamento a 'Separacion'
        if ($separacion->proforma && $separacion->proforma->departamento) {
            $estadoSeparacion = EstadoDepartamento::where('nombre', 'Separacion')->first();

            if ($estadoSeparacion) {
                $separacion->proforma->departamento->update([
                    'estado_departamento_id' => $estadoSeparacion->id
                ]);
            }
        }

        if ($separacion->proforma && $separacion->proforma->prospecto) {
            $separacion->proforma->prospecto->update([
                'tipo_gestion_id' => 6,
            ]);
        }

        Notification::make()
            ->title('Separación Definitiva creada exitosamente')
            ->success()
            ->send();

        // Emitir eventos para refrescar el panel de seguimientos
        $this->emit('refreshTable');
        $this->emit('tareaCreada');
        
        // Forzar reload completo de la página
        $this->dispatchBrowserEvent('reload-page');
    }

    protected function getRedirectUrl(): string
    {
        // Si viene desde el panel de seguimientos, redirigir de vuelta
        $numeroDocumento = request()->get('numero_documento');
        if ($numeroDocumento) {
            return route('filament.admin.resources.panel-seguimiento.index');
        }

        return SeparacionResource::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = 1;
        $data['updated_by'] = 1;

        return $data;
    }
}