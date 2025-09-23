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

        // Calcular precio_venta con la nueva fórmula correcta
        if (isset($data['departamento_id']) && isset($data['precio_lista']) && isset($data['descuento'])) {
            $departamento = \App\Models\Departamento::find($data['departamento_id']);
            if ($departamento) {
                $precioVentaOriginal = $departamento->Precio_venta;
                // Calcular precio lista con descuento aplicado
                $precioListaConDescuento = $data['precio_lista'] - ($data['precio_lista'] * $data['descuento'] / 100);
                // Restar el precio lista con descuento del precio venta original
                $data['precio_venta'] = $precioVentaOriginal - $precioListaConDescuento;
            }
        }

        // Calcular monto_cuota_inicial automáticamente
        if (isset($data['precio_venta']) && isset($data['monto_separacion'])) {
            $data['monto_cuota_inicial'] = $data['precio_venta'] - $data['monto_separacion'];
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
