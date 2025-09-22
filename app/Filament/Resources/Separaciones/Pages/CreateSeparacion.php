<?php

namespace App\Filament\Resources\Separaciones\Pages;

use App\Models\Separacion;
use App\Models\EstadoDepartamento;

use App\Filament\Resources\Separaciones\SeparacionResource;
use App\Filament\Resources\PanelSeguimientoResource;
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

        // Cambiar el estado del departamento a 'Separacion' solo si la proforma tiene prospecto
        if ($separacion->proforma && 
            $separacion->proforma->departamento && 
            $separacion->proforma->prospecto_id) {
            $estadoSeparacion = EstadoDepartamento::where('nombre', 'Separacion')->first();

            if ($estadoSeparacion) {
                $separacion->proforma->departamento->update([
                    'estado_departamento_id' => $estadoSeparacion->id
                ]);
            }
        }

        // Solo actualizar el estado del prospecto si la proforma tiene un prospecto asociado
        if ($separacion->proforma && 
            $separacion->proforma->prospecto && 
            $separacion->proforma->prospecto_id) {
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
        
        // Forzar reload completo de la página del panel de seguimientos
        $this->dispatchBrowserEvent('reload-page');
        $this->dispatchBrowserEvent('refresh-panel-seguimiento');
    }

    protected function getRedirectUrl(): string
    {
        // Agregar parámetro de reload para forzar actualización de datos
        return PanelSeguimientoResource::getUrl('index') . '?reload=' . time();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = 1;
        $data['updated_by'] = 1;

        return $data;
    }
}