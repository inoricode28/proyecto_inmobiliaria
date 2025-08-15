<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\Separacion;
use App\Models\EstadoDepartamento;
use Barryvdh\DomPDF\Facade\Pdf; // Agregar esta línea

class CreateVenta extends CreateRecord
{
    protected static string $resource = VentaResource::class;

    // Agregar propiedades públicas para los campos de fecha
    public $fecha_entrega_inicial;
    public $fecha_venta;
    public $fecha_preminuta;
    public $fecha_minuta;
    public $estado;

    public function mount(): void
    {
        parent::mount();
        
        // Inicializar las propiedades
        $this->fecha_entrega_inicial = null;
        $this->fecha_venta = null;
        $this->fecha_preminuta = null;
        $this->fecha_minuta = null;
        $this->estado = 'activo';

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
                // Agregar las fechas por defecto
                'fecha_venta' => now()->format('Y-m-d'),
                'fecha_entrega_inicial' => now()->format('Y-m-d'),
                'fecha_preminuta' => now()->format('Y-m-d'),
                'fecha_minuta' => now()->format('Y-m-d'),
                'estado' => 'activo',
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

    // Agregar estos métodos públicos para manejar la generación de pre-minutas
    public function generatePreMinutaWord()
    {
        try {
            // Validar que hay datos suficientes para generar el documento
            if (empty($this->data['separacion_id'])) {
                Notification::make()
                    ->title('Error')
                    ->body('Debe seleccionar una separación antes de generar la pre-minuta.')
                    ->warning()
                    ->send();
                return;
            }

            // Obtener datos de la separación
            $separacion = \App\Models\Separacion::with([
                'proforma.tipoDocumento',
                'proforma.genero', 
                'proforma.proyecto',
                'proforma.departamento'
            ])->find($this->data['separacion_id']);

            if (!$separacion) {
                Notification::make()
                    ->title('Error')
                    ->body('Separación no encontrada.')
                    ->danger()
                    ->send();
                return;
            }

            // Generar documento Word usando PhpWord
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $section = $phpWord->addSection();
            
            // Agregar contenido al documento
            $section->addTitle('PRE-MINUTA DE VENTA', 1);
            $section->addTextBreak(2);
            
            $section->addText('Cliente: ' . $separacion->proforma->nombres . ' ' . $separacion->proforma->apellidos);
            $section->addText('Proyecto: ' . $separacion->proforma->proyecto->nombre);
            $section->addText('Departamento: ' . $separacion->proforma->departamento->numero);
            $section->addText('Fecha: ' . now()->format('d/m/Y'));
            
            // Guardar y descargar
            $fileName = 'pre-minuta-' . $separacion->codigo . '-' . now()->format('Y-m-d') . '.docx';
            $tempFile = storage_path('app/temp/' . $fileName);
            
            // Crear directorio si no existe
            if (!file_exists(dirname($tempFile))) {
                mkdir(dirname($tempFile), 0755, true);
            }
            
            $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($tempFile);
            
            // Retornar descarga sin crear venta
            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Error al generar Pre-Minuta WORD: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function generatePreMinutaPDF()
    {
        try {
            // Validar que hay datos suficientes para generar el documento
            if (empty($this->data['separacion_id'])) {
                Notification::make()
                    ->title('Error')
                    ->body('Debe seleccionar una separación antes de generar la pre-minuta.')
                    ->warning()
                    ->send();
                return;
            }

            // Obtener datos de la separación
            $separacion = \App\Models\Separacion::with([
                'proforma.tipoDocumento',
                'proforma.genero',
                'proforma.proyecto', 
                'proforma.departamento'
            ])->find($this->data['separacion_id']);

            if (!$separacion) {
                Notification::make()
                    ->title('Error')
                    ->body('Separación no encontrada.')
                    ->danger()
                    ->send();
                return;
            }

            // Generar PDF usando DomPDF
            $pdf = Pdf::loadView('pdf.pre-minuta', compact('separacion'));
            
            $fileName = 'pre-minuta-' . $separacion->codigo . '-' . now()->format('Y-m-d') . '.pdf';
            $tempFile = storage_path('app/temp/' . $fileName);
            
            // Crear directorio si no existe
            if (!file_exists(dirname($tempFile))) {
                mkdir(dirname($tempFile), 0755, true);
            }
            
            // Guardar el PDF temporalmente
            $pdf->save($tempFile);
            
            // Retornar descarga igual que el WORD
            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('Error al generar Pre-Minuta PDF: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}