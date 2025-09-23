<?php

namespace App\Filament\Resources\Proforma\ProformaResource\Pages;

use App\Filament\Resources\Proforma\ProformaResource;
use App\Filament\Resources\PanelSeguimientoResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\Prospecto;

class CreateProforma extends CreateRecord
{
    protected static string $resource = ProformaResource::class;

    protected function afterCreate()
    {
        $proforma = $this->record;

        // Cargar la relación del prospecto si no está cargada
        if (!$proforma->relationLoaded('prospecto')) {
            $proforma->load('prospecto');
        }

        if ($proforma->prospecto) {
            try {
                $proforma->prospecto->update([
                    'tipo_gestion_id' => 5, // Visitas
                ]);

                // Log para verificar que la actualización se realizó
            } catch (\Exception $e) {
            }
        }

        Notification::make()
            ->title('Proforma creada exitosamente')
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

    protected function getFormMaxWidth(): string|null
    {
        return '7x1';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
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

        $data['created_by'] = 1;
        $data['updated_by'] = 1;

        return $data;
    }

    public function mount(): void
    {
        parent::mount();

        // Verificar si viene el parámetro prospecto_id desde seguimientos
        $prospectoId = request()->get('prospecto_id');

        if ($prospectoId) {
            $prospecto = Prospecto::find($prospectoId);

            if ($prospecto) {
                // Precargar TODOS los datos del prospecto disponibles
                $formData = [
                    'prospecto_id' => $prospecto->id,
                    'nombres' => $prospecto->nombres,
                    'ape_paterno' => $prospecto->ape_paterno,
                    'ape_materno' => $prospecto->ape_materno,
                    'razon_social' => $prospecto->razon_social,
                    'celular' => $prospecto->celular,
                    'numero_documento' => $prospecto->numero_documento,
                    'tipo_documento_id' => $prospecto->tipo_documento_id,
                    'proyecto_id' => $prospecto->proyecto_id,
                    'tipo_inmueble_id' => $prospecto->tipo_inmueble_id,
                    'forma_contacto_id' => $prospecto->forma_contacto_id,
                    'como_se_entero_id' => $prospecto->como_se_entero_id,
                ];

                // Campos adicionales que podrían estar en el formulario de proforma
                //if ($prospecto->fecha_registro) {
                   // $formData['fecha_registro'] = $prospecto->fecha_registro;
                //}

                // Asignar el correo electrónico tanto en correo como en email
                $formData['correo'] = $prospecto->correo_electronico;
                $formData['email'] = $prospecto->correo_electronico;

                // Asignar fecha de vencimiento (fecha actual + 2 días)
                $formData['fecha_vencimiento'] = now()->addDays(2)->format('Y-m-d');

                // Llenar el formulario con los datos del prospecto
                $this->form->fill($formData);
            }
        }
    }

}
