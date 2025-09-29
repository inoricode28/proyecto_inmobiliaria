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
        // Validar descuento
        if (isset($data['descuento']) && $data['descuento'] !== null) {
            if ($data['descuento'] < 0 || $data['descuento'] > 5) {
                throw new \Exception('El descuento debe estar entre 0% y 5%');
            }
        }

        // Calcular precio_venta según la lógica implementada en el formulario
        if (isset($data['departamento_id'])) {
            $departamento = \App\Models\Departamento::find($data['departamento_id']);
            if ($departamento) {
                // Si hay descuento y es mayor a 0, calcular precio con descuento
                if (isset($data['descuento']) && $data['descuento'] > 0) {
                    $descuento = floatval($data['descuento']);
                    $montoDescuento = $departamento->Precio_lista * ($descuento / 100);
                    $data['precio_venta'] = $departamento->Precio_lista - $montoDescuento;
                } else {
                    // Si no hay descuento o es 0%, usar el precio_venta original del inmueble
                    $data['precio_venta'] = $departamento->Precio_venta;
                }
                
                // Asegurar que precio_lista esté establecido
                $data['precio_lista'] = $departamento->Precio_lista;
            }
        }

        // Calcular monto_cuota_inicial como 10% del precio_venta
        if (isset($data['precio_venta'])) {
            $data['monto_cuota_inicial'] = $data['precio_venta'] * 0.10;
        }

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
