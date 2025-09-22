<?php

namespace App\Filament\Resources\Proforma\ProformaResource\Pages;

use App\Filament\Resources\Proforma\ProformaResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditProforma extends EditRecord
{
    protected static string $resource = ProformaResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = Auth::id(); // o auth()->id()
        return $data;
    }

    protected function afterSave(): void
    {
        $proforma = $this->record;
        
        // Si se asignó un prospecto a la proforma
        if ($proforma->prospecto_id) {
            $prospecto = $proforma->prospecto;
            
            if ($prospecto) {
                // Verificar si la proforma tiene separación
                $tieneSeparacion = $proforma->separacion()->exists();
                
                if ($tieneSeparacion) {
                    // Si tiene separación, cambiar estado a "Separación" (ID: 6)
                    $prospecto->update(['tipo_gestion_id' => 6]);
                    
                    // También actualizar el estado del departamento a "Separación"
                    if ($proforma->departamento) {
                        $estadoSeparacion = \App\Models\EstadoDepartamento::where('nombre', 'Separacion')->first();
                        if ($estadoSeparacion) {
                            $proforma->departamento->update([
                                'estado_departamento_id' => $estadoSeparacion->id
                            ]);
                        }
                    }
                } else {
                    // Si no tiene separación, cambiar estado a "Visitas" (ID: 5)
                    $prospecto->update(['tipo_gestion_id' => 5]);
                }
            }
        }
    }
    
}
