<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\Separacion;
use App\Models\EstadoDepartamento;

class CreateVenta extends CreateRecord
{
    protected static string $resource = VentaResource::class;

    public function mount(): void
    {
        parent::mount();

        // Mejorar la URL con parámetros de pre-validación
        if (request()->has('separacion_id')) {
            $separacionId = request()->get('separacion_id');

            // Verificar que la separación existe y no tiene venta asociada
            $separacion = Separacion::with('proforma.proyecto')
                ->whereDoesntHave('venta')
                ->find($separacionId);

            if ($separacion) {
                // Validaciones adicionales si se proporcionan parámetros extra
                $isValid = true;


                if ($isValid) {
                    // Llenar el formulario con el separacion_id
                    $this->form->fill(['separacion_id' => $separacionId]);
                    
                    // Forzar la actualización del estado del campo separacion_id
                    $this->data['separacion_id'] = $separacionId;
                    
                    // Ejecutar la población manual de datos
                    $this->populateFromSeparacion($separacionId);
                    
                    // Forzar la actualización del formulario para reflejar los cambios
                    $this->form->fill($this->data);

                    // Notificación de éxito
                    Notification::make()
                        ->title('Separación cargada')
                        ->body('Los datos del cliente han sido cargados correctamente.')
                        ->success()
                        ->send();
                }
            } else {
                Notification::make()
                    ->title('Separación no válida')
                    ->body('La separación no existe o ya tiene una venta asociada.')
                    ->warning()
                    ->send();
            }
        }
    }

    protected function populateFromSeparacion($separacionId)
    {
        $separacion = \App\Models\Separacion::with([
            'proforma.tipoDocumento',
            'proforma.genero',
            'proforma.nacionalidad',
            'proforma.estadoCivil',
            'proforma.gradoEstudio',
            'proforma.proyecto',
            'proforma.departamento',
            'ocupacion',
            'profesion',
            'categoria',
            'notariaKardex',
            'cartaFianza'
        ])->find($separacionId);

        if ($separacion && $separacion->proforma) {
            $proforma = $separacion->proforma;

            // Preparar los datos para llenar el formulario
            $formData = [
                'separacion_id' => $separacionId,
                'tipo_documento_nombre' => optional($proforma->tipoDocumento)->nombre,
                'numero_documento' => $proforma->numero_documento,
                'nombres' => $proforma->nombres,
                'ape_paterno' => $proforma->ape_paterno,
                'ape_materno' => $proforma->ape_materno,
                'razon_social' => $proforma->razon_social,
                'genero_nombre' => optional($proforma->genero)->nombre,
                'fecha_nacimiento' => $proforma->fecha_nacimiento,
                'nacionalidad_nombre' => optional($proforma->nacionalidad)->nombre,
                'estadoCivil_nombre' => optional($proforma->estadoCivil)->nombre,
                'gradoEstudio_nombre' => optional($proforma->gradoEstudio)->nombre,
                'telefono_casa' => $proforma->telefono_casa,
                'celular' => $proforma->celular,
                'email' => $proforma->email,
                 'direccion' => $proforma->direccion,
                 'direccion_adicional' => $proforma->direccion_adicional,
                'ocupacion_nombre' => optional($separacion->ocupacion)->nombre,
                'profesion_nombre' => optional($separacion->profesion)->nombre,
                'categoria_nombre' => optional($separacion->categoria)->nombre,
                'notaria_kardex_nombre' => optional($separacion->notariaKardex)->nombre,
                'carta_fianza_nombre' => optional($separacion->cartaFianza)->nombre,
                'proyecto_nombre' => optional($proforma->proyecto)->nombre,
                 'departamento_nombre' => optional($proforma->departamento)->num_departamento,
                'precio_lista' => $proforma->departamento->precio ?? 0,
                 'precio_venta' => $proforma->departamento->Precio_venta ?? 0,
                 'descuento' => $proforma->departamento->descuento ?? 0,
                 'monto_separacion' => $proforma->monto_separacion,
                 'cuota_inicial' => $proforma->monto_cuota_inicial,
                 'tipo_separacion' => $separacion->tipo_separacion,
                 'numero_partida' => $separacion->numero_partida,
                 'lugar_partida' => $separacion->lugar_partida,
                 'porcentaje_copropietario' => $separacion->porcentaje_copropietario,
                 'puesto' => $separacion->puesto,
                 'ruc' => $separacion->ruc,
                 'empresa' => $separacion->empresa,
                 'pep' => $separacion->pep,
                 'fecha_pep' => $separacion->fecha_pep,
                 'direccion_laboral' => $separacion->direccion_laboral,
                 'urbanizacion' => $separacion->urbanizacion,
                 'telefono1' => $separacion->telefono1,
                 'telefono2' => $separacion->telefono2,
                 'antiguedad_laboral' => $separacion->antiguedad_laboral,
                 'ingresos' => $separacion->ingresos,
                 'saldo_a_financiar' => $separacion->saldo_a_financiar,
                 'observaciones_separacion' => $separacion->observaciones,
                 'documentos_separacion' => $separacion->documentos,

                 // Datos de ubicación
                 'departamento_ubigeo_nombre' => optional($proforma->ubigeoDepartamento)->nombre,
                 'provincia_nombre' => optional($proforma->ubigeoProvincia)->nombre,
                 'distrito_nombre' => optional($proforma->ubigeoDistrito)->nombre,
             ];

             // Datos de notaría kardex
             if ($separacion->notariaKardex) {
                 $notaria = $separacion->notariaKardex;
                 $formData = array_merge($formData, [
                     'notaria_nombre' => $notaria->notaria,
                     'notaria_responsable' => $notaria->responsable,
                     'notaria_direccion' => $notaria->direccion,
                     'notaria_email' => $notaria->email,
                     'notaria_celular' => $notaria->celular,
                     'notaria_telefono' => $notaria->telefono,
                     'numero_kardex' => $notaria->numero_kardex,
                     'oficina' => $notaria->oficina,
                     'numero_registro' => $notaria->numero_registro,
                     'agencia' => $notaria->agencia,
                     'asesor' => $notaria->asesor,
                     'telefonos' => $notaria->telefonos,
                     'correos' => $notaria->correos,
                     'fecha_vencimiento_carta' => $notaria->fecha_vencimiento_carta,
                     'fecha_escritura' => $notaria->fecha_escritura_publica,
                     'penalidad_entrega' => $notaria->penalidad_entrega,
                 ]);
             }

             // Datos de carta fianza
             if ($separacion->cartaFianza) {
                 $carta = $separacion->cartaFianza;
                 $formData = array_merge($formData, [
                     'carta_banco_nombre' => optional($carta->banco)->nombre,
                     'carta_monto' => $carta->monto,
                     'carta_numero' => $carta->numero_carta,
                 ]);
             }
             
             // Actualizar tanto $this->data como el formulario
             $this->data = array_merge($this->data, $formData);
             $this->form->fill($formData);
        }
    }

    protected function getRedirectUrl(): string
    {
        return VentaResource::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $venta = $this->record;

        // Cambiar el estado del departamento a 'Minuta'
        if ($venta->separacion && $venta->separacion->proforma && $venta->separacion->proforma->departamento) {
            $estadoMinuta = EstadoDepartamento::where('nombre', 'Minuta')->first();
            
            if ($estadoMinuta) {
                $venta->separacion->proforma->departamento->update([
                    'estado_departamento_id' => $estadoMinuta->id
                ]);
            }
        }

        Notification::make()
            ->title('Venta creada exitosamente')
            ->success()
            ->send();
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Venta creada')
            ->body('La venta ha sido creada exitosamente.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = 1; 
        $data['updated_by'] = 1; 

        return $data;
    }
}