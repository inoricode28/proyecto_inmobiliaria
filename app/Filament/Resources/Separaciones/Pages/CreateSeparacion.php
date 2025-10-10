<?php

namespace App\Filament\Resources\Separaciones\Pages;

use App\Models\Separacion;
use App\Models\SeparacionInmueble;
use App\Models\EstadoDepartamento;
use App\Models\Prospecto;
use App\Models\Proforma;
use App\Models\CronogramaCuotaInicial;
use App\Models\TipoCuota;
use App\Models\EstadoCuota;

use App\Filament\Resources\Separaciones\SeparacionResource;
use App\Filament\Resources\PanelSeguimientoResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreateSeparacion extends CreateRecord
{
    protected static string $resource = SeparacionResource::class;

    public function mount(): void
    {
        parent::mount();

        // Detectar si viene desde separación definitiva o desde panel de seguimiento
        $fromSeparacionDefinitiva = request('from') === 'separacion_definitiva';
        $numeroDocumento = request('numero_documento');
        $prospectoId = request('prospecto_id');
        $proformaId = request('proforma_id');

        // Priorizar prospecto_id si está disponible
        if ($prospectoId) {
            // Buscar proforma por prospecto_id
            $proforma = Proforma::where('prospecto_id', $prospectoId)->first();
        } elseif ($proformaId) {
            // Si no hay prospecto_id, buscar directamente por proforma_id
            $proforma = Proforma::find($proformaId);
        } elseif ($numeroDocumento) {
            // Buscar una proforma existente para este número de documento o email
            // Incluir búsqueda por razón social para clientes sin DNI
            $proforma = Proforma::where(function($query) use ($numeroDocumento) {
                $query->where('numero_documento', $numeroDocumento)
                      ->orWhere('email', $numeroDocumento)
                      ->orWhere('razon_social', 'LIKE', "%{$numeroDocumento}%");
            })->first();
        }

        if (isset($proforma) && $proforma) {
            // Pre-llenar el formulario con la proforma encontrada
            $fillData = [
                'proforma_id' => $proforma->id,
                'tipo_documento_nombre' => optional($proforma->tipoDocumento)->nombre,
                'numero_documento' => $proforma->numero_documento,
                'nombres' => $proforma->nombres,
                'ape_paterno' => $proforma->ape_paterno,
                'ape_materno' => $proforma->ape_materno,
                'razon_social' => $proforma->razon_social,
                'genero_id' => $proforma->genero_id,
                'genero_nombre' => optional($proforma->genero)->nombre,
                'fecha_nacimiento' => $proforma->fecha_nacimiento,
                'nacionalidad_nombre' => optional($proforma->nacionalidad)->nombre,
                'estado_civil_id' => $proforma->estado_civil_id,
                'estadoCivil_nombre' => optional($proforma->estadoCivil)->nombre,
                'gradoEstudio_nombre' => optional($proforma->gradoEstudio)->nombre,
                'telefono_casa' => $proforma->telefono_casa,
                'celular' => $proforma->celular,
                'email' => $proforma->email,
                'direccion' => $proforma->direccion,
                'departamento_ubigeo_id' => $proforma->departamento_ubigeo_id,
                'provincia_id' => $proforma->provincia_id,
                'distrito_id' => $proforma->distrito_id,
                'direccion_adicional' => $proforma->direccion_adicional,
                'monto_separacion' => $proforma->monto_separacion,
                'cuota_inicial' => $proforma->monto_cuota_inicial,
                'proyecto_nombre' => optional($proforma->proyecto)->nombre,
                'departamento_nombre' => optional($proforma->departamento)->num_departamento,
                'precio_lista' => $proforma->departamento->Precio_lista ?? null,
                'precio_venta' => $proforma->precio_venta ?? null,
                'descuento' => $proforma->descuento,
                // NO establecer departamento_id aquí - se establecerá desde el frontend
            ];

            // Solo agregar el flag si viene desde separación definitiva
            if ($fromSeparacionDefinitiva) {
                $fillData['from_separacion_definitiva'] = true;
            }

            $this->form->fill($fillData);
        }
    }

    protected function handleRecordCreation(array $data): \App\Models\Separacion
    {
        $notariaData = $data['notaria_kardex'] ?? [];
        $cartaData = $data['carta_fianza'] ?? [];

        unset($data['notaria_kardex'], $data['carta_fianza']);

        $separacion = Separacion::create($data);

        // Crear relaciones
        if (!empty($notariaData)) {
            $separacion->notariaKardex()->create($notariaData);
        }

        if (!empty($cartaData)) {
            $separacion->cartaFianza()->create($cartaData);
        }

        return $separacion;
    }

    protected function afterCreate()
    {
        $separacion = $this->record;

        // Obtener TODOS los departamentos seleccionados del formulario
        $departamentosSeleccionados = $this->getInmueblesSeleccionados();

        if (empty($departamentosSeleccionados)) {
            Log::error('No se encontraron departamentos seleccionados en los datos del formulario', [
                'data' => $this->data,
                'separacion_id' => $separacion->id
            ]);

            Notification::make()
                ->title('Error: No se seleccionó ningún inmueble')
                ->danger()
                ->send();
            return;
        }

        Log::info('Procesando múltiples departamentos seleccionados', [
            'separacion_id' => $separacion->id,
            'departamentos_count' => count($departamentosSeleccionados),
            'departamentos' => $departamentosSeleccionados
        ]);

        $procesadosExitosos = 0;
        $errores = [];

        // Cambiar el estado de TODOS los departamentos seleccionados a 'Separacion'
        if ($separacion->proforma) {
            $estadoSeparacion = EstadoDepartamento::where('nombre', 'Separacion')->first();

            if ($estadoSeparacion) {
                foreach ($departamentosSeleccionados as $index => $departamentoId) {
                    try {
                        Log::info("Procesando departamento {$index}: {$departamentoId}", [
                            'separacion_id' => $separacion->id,
                            'departamento_id' => $departamentoId
                        ]);

                        // Buscar el inmueble específico seleccionado
                        $inmuebleSeleccionado = null;

                        // Primero buscar en proformaInmuebles
                        if (method_exists($separacion->proforma, 'proformaInmuebles') && $separacion->proforma->proformaInmuebles()->exists()) {
                            $inmuebleSeleccionado = $separacion->proforma->proformaInmuebles()
                                ->with('departamento')
                                ->where('departamento_id', $departamentoId)
                                ->first();
                        }

                        // Si no se encuentra en proformaInmuebles, buscar directamente el departamento
                        if (!$inmuebleSeleccionado) {
                            $departamento = \App\Models\Departamento::find($departamentoId);
                            if ($departamento) {
                                $inmuebleSeleccionado = (object) [
                                    'departamento_id' => $departamento->id,
                                    'departamento' => $departamento,
                                    'precio_lista' => $departamento->Precio_lista ?? 0,
                                    'precio_venta' => $separacion->precio_venta ?? $departamento->Precio_lista ?? 0,
                                    'monto_separacion' => $separacion->monto_separacion ?? 0,
                                    'monto_cuota_inicial' => $separacion->cuota_inicial ?? 0,
                                    'saldo_financiar' => $separacion->saldo_a_financiar ?? 0,
                                ];
                            }
                        }

                        // Procesar el inmueble seleccionado
                        if ($inmuebleSeleccionado) {
                            $departamento = $inmuebleSeleccionado->departamento ?? \App\Models\Departamento::find($inmuebleSeleccionado->departamento_id);

                            if ($departamento) {
                                // Verificar si el departamento ya está separado
                                if ($departamento->estado_departamento_id == $estadoSeparacion->id) {
                                    Log::warning("El departamento {$departamentoId} ya está en estado Separacion", [
                                        'departamento_id' => $departamentoId,
                                        'separacion_id' => $separacion->id
                                    ]);
                                    continue; // Saltar este departamento
                                }

                                // Actualizar estado del departamento
                                $departamento->update([
                                    'estado_departamento_id' => $estadoSeparacion->id,
                                ]);

                                // Calcular valores del inmueble seleccionado usando los datos de la separación
                                $precioLista = $inmuebleSeleccionado->precio_lista ?? $departamento->Precio_lista ?? 0;
                                $precioVenta = $separacion->precio_venta ?? $inmuebleSeleccionado->precio_venta ?? $precioLista;
                                $montoSeparacion = $separacion->monto_separacion ?? $inmuebleSeleccionado->monto_separacion ?? 0;
                                $montoCuotaInicial = $separacion->cuota_inicial ?? $inmuebleSeleccionado->monto_cuota_inicial ?? 0;
                                $saldoFinanciar = $separacion->saldo_a_financiar ?? $inmuebleSeleccionado->saldo_financiar ?? 0;

                                // Crear registro en separacion_inmuebles para CADA inmueble
                                SeparacionInmueble::create([
                                    'separacion_id' => $separacion->id,
                                    'departamento_id' => $departamento->id,
                                    'precio_lista' => $precioLista,
                                    'precio_venta' => $precioVenta,
                                    'monto_separacion' => $montoSeparacion,
                                    'monto_cuota_inicial' => $montoCuotaInicial,
                                    'saldo_financiar' => $saldoFinanciar,
                                    'orden' => $index + 1, // Orden secuencial
                                    'created_by' => Auth::id() ?? 1,
                                    'updated_by' => Auth::id() ?? 1,
                                ]);

                                $procesadosExitosos++;
                                Log::info("Departamento {$departamentoId} procesado exitosamente", [
                                    'departamento_id' => $departamento->id,
                                    'estado_departamento_id' => $estadoSeparacion->id,
                                    'proforma_id' => $separacion->proforma->id,
                                    'separacion_id' => $separacion->id,
                                    'orden' => $index + 1
                                ]);
                            } else {
                                $errores[] = "No se encontró el departamento con ID: {$departamentoId}";
                                Log::error("No se encontró el departamento con ID: {$departamentoId}");
                            }
                        } else {
                            $errores[] = "No se pudo encontrar información del inmueble con ID: {$departamentoId}";
                            Log::error("No se pudo encontrar información del inmueble con ID: {$departamentoId}");
                        }
                    } catch (\Exception $e) {
                        $errorMsg = "Error procesando departamento {$departamentoId}: " . $e->getMessage();
                        $errores[] = $errorMsg;
                        Log::error($errorMsg, [
                            'departamento_id' => $departamentoId,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }
            } else {
                $errores[] = 'No se encontró el estado "Separacion" en la base de datos';
                Log::error('No se encontró el estado "Separacion" en la base de datos');
            }
        } else {
            $errores[] = 'No se encontró la proforma asociada a la separación';
            Log::error('No se encontró la proforma asociada a la separación', [
                'separacion_id' => $separacion->id,
                'proforma_id' => $separacion->proforma_id
            ]);
        }

        // Actualizar el estado del prospecto
        if ($separacion->proforma &&
            ($separacion->proforma->prospecto_id || $this->data['from_separacion_definitiva'] ?? false)) {

            // Si hay prospecto asociado, actualizarlo
            if ($separacion->proforma->prospecto) {
                $separacion->proforma->prospecto->update([
                    'tipo_gestion_id' => 6,
                ]);

                Log::info('Prospecto actualizado a tipo gestión 6', [
                    'prospecto_id' => $separacion->proforma->prospecto->id,
                    'separacion_id' => $separacion->id
                ]);
            }
            // Si viene desde separación definitiva pero no hay prospecto, buscar por datos de la proforma
            elseif ($this->data['from_separacion_definitiva'] ?? false) {
                $prospecto = \App\Models\Prospecto::where('numero_documento', $separacion->numero_documento)
                    ->orWhere('email', $separacion->email)
                    ->first();

                if ($prospecto) {
                    $prospecto->update([
                        'tipo_gestion_id' => 6,
                    ]);

                    Log::info('Prospecto encontrado por datos y actualizado a tipo gestión 6', [
                        'prospecto_id' => $prospecto->id,
                        'separacion_id' => $separacion->id
                    ]);
                }
            }
        }

        // Crear cronograma de cuotas por defecto si hay datos de la proforma
        $this->crearCronogramaPorDefecto($separacion);

        // Mostrar notificación con resumen
        if ($procesadosExitosos > 0) {
            $mensaje = "Separación creada exitosamente. Se procesaron {$procesadosExitosos} inmueble(s).";

            if (!empty($errores)) {
                $mensaje .= " Se encontraron " . count($errores) . " error(es).";
                // Mostrar primeros 3 errores en el body de la notificación
                $primerosErrores = array_slice($errores, 0, 3);
                Notification::make()
                    ->title($mensaje)
                    ->body(implode(', ', $primerosErrores))
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title($mensaje)
                    ->success()
                    ->send();
            }

            Log::info('Resumen de procesamiento de separación', [
                'separacion_id' => $separacion->id,
                'procesados_exitosos' => $procesadosExitosos,
                'errores' => count($errores),
                'total_departamentos' => count($departamentosSeleccionados)
            ]);
        } else {
            Notification::make()
                ->title('Error: No se pudo procesar ningún inmueble')
                ->body(!empty($errores) ? implode(', ', array_slice($errores, 0, 3)) : 'No se encontraron inmuebles para procesar')
                ->danger()
                ->send();
        }

        // Emitir eventos para refrescar el panel de seguimientos
        $this->emit('refreshTable');
        $this->emit('tareaCreada');

        // Forzar reload completo de la página del panel de seguimientos
        $this->dispatchBrowserEvent('reload-page');
        $this->dispatchBrowserEvent('refresh-panel-seguimiento');
    }

    /**
     * Método auxiliar para obtener los inmuebles seleccionados
     */
    protected function getInmueblesSeleccionados(): array
    {
        $inmuebles = $this->data['inmuebles_seleccionados'] ?? [];

        // Si es string (JSON), convertirlo a array
        if (is_string($inmuebles)) {
            $decoded = json_decode($inmuebles, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $inmuebles = $decoded;
            } else {
                // Intentar formato CSV: "1,2,3"
                $csvParts = preg_split('/\s*,\s*/', trim($inmuebles));
                $inmuebles = is_array($csvParts) ? $csvParts : [];
            }
        }

        // Si está vacío, intentar con departamento_id individual (para compatibilidad)
        if (empty($inmuebles)) {
            $departamentoUnico = $this->data['departamento_id'] ?? null;
            if ($departamentoUnico) {
                $inmuebles = [$departamentoUnico];
            }
        }

        // Asegurar que sea un array y filtrar valores vacíos
        $inmuebles = is_array($inmuebles) ? $inmuebles : [];
        $inmuebles = array_filter($inmuebles, function($item) {
            return !empty($item) && $item !== null;
        });

        // Convertir todos los valores a enteros
        $inmuebles = array_map(function($item){
            return (int) (is_array($item) ? ($item['departamento_id'] ?? $item[0] ?? $item) : $item);
        }, $inmuebles);

        Log::info('Inmuebles seleccionados procesados', [
            'original' => $this->data['inmuebles_seleccionados'] ?? 'no existe',
            'procesado' => $inmuebles,
            'count' => count($inmuebles)
        ]);

        return $inmuebles;
    }

    /**
     * Crear cronograma de cuotas por defecto basado en la proforma
     */
    protected function crearCronogramaPorDefecto(Separacion $separacion)
    {
        try {
            if (!$separacion->proforma) {
                return;
            }

            $proforma = $separacion->proforma;

            // Primero, buscar si hay cuotas temporales para esta proforma
            $cuotasTemporales = CronogramaCuotaInicial::where('proforma_id', $proforma->id)
                ->whereNull('separacion_id')
                ->get();

            if ($cuotasTemporales->count() > 0) {
                // Si hay cuotas temporales, asignarles el ID de la separación
                Log::info('Asignando separacion_id a cuotas temporales', [
                    'separacion_id' => $separacion->id,
                    'proforma_id' => $proforma->id,
                    'cuotas_count' => $cuotasTemporales->count()
                ]);

                foreach ($cuotasTemporales as $cuotaTemporal) {
                    $cuotaTemporal->update([
                        'separacion_id' => $separacion->id,
                        'updated_by' => Auth::id() ?? 1,
                    ]);
                }

                Log::info('Cuotas temporales actualizadas con separacion_id', [
                    'separacion_id' => $separacion->id,
                    'cuotas_actualizadas' => $cuotasTemporales->count()
                ]);

                return; // No crear cuota por defecto si ya hay cuotas temporales
            }

            // Buscar si hay cuotas de saldo a financiar temporales para esta proforma
            $cuotasSaldoFinanciarTemporales = \App\Models\CronogramaSaldoFinanciar::where('proforma_id', $proforma->id)
                ->whereNull('separacion_id')
                ->get();

            if ($cuotasSaldoFinanciarTemporales->count() > 0) {
                // Si hay cuotas de saldo a financiar temporales, asignarles el ID de la separación
                Log::info('Asignando separacion_id a cuotas de saldo a financiar temporales', [
                    'separacion_id' => $separacion->id,
                    'proforma_id' => $proforma->id,
                    'cuotas_sf_count' => $cuotasSaldoFinanciarTemporales->count()
                ]);

                foreach ($cuotasSaldoFinanciarTemporales as $cuotaSFTemporal) {
                    $cuotaSFTemporal->update([
                        'separacion_id' => $separacion->id,
                        'updated_by' => Auth::id() ?? 1,
                    ]);
                }

                Log::info('Cuotas de saldo a financiar temporales actualizadas con separacion_id', [
                    'separacion_id' => $separacion->id,
                    'cuotas_sf_actualizadas' => $cuotasSaldoFinanciarTemporales->count()
                ]);
            }

            // Buscar si hay pagos de separación temporales para esta proforma
            $pagosTemporales = DB::table('pagos_separacion')
                ->where('proforma_id', $proforma->id)
                ->whereNull('separacion_id')
                ->get();

            if ($pagosTemporales->count() > 0) {
                // Si hay pagos temporales, asignarles el ID de la separación
                Log::info('Asignando separacion_id a pagos de separación temporales', [
                    'separacion_id' => $separacion->id,
                    'proforma_id' => $proforma->id,
                    'pagos_count' => $pagosTemporales->count()
                ]);

                DB::table('pagos_separacion')
                    ->where('proforma_id', $proforma->id)
                    ->whereNull('separacion_id')
                    ->update([
                        'separacion_id' => $separacion->id,
                        'updated_at' => now()
                    ]);

                Log::info('Pagos de separación temporales actualizados con separacion_id', [
                    'separacion_id' => $separacion->id,
                    'pagos_actualizados' => $pagosTemporales->count()
                ]);
            }

            // Si no hay cuotas temporales, crear cronograma por defecto
            $montoTotal = $proforma->monto_cuota_inicial ?? 0;

            // Solo crear cronograma si hay un monto válido
            if ($montoTotal <= 0) {
                return;
            }

            // Obtener tipos de cuota
            $tipoCuotaInicial = TipoCuota::where('nombre', 'Cuota Inicial')->first();
            if (!$tipoCuotaInicial) {
                $tipoCuotaInicial = TipoCuota::create([
                    'nombre' => 'Cuota Inicial',
                    'descripcion' => 'Primera cuota del cronograma',
                    'activo' => true
                ]);
            }

            // Obtener estado pendiente
            $estadoPendiente = EstadoCuota::where('nombre', 'Pendiente')->first();
            if (!$estadoPendiente) {
                $estadoPendiente = EstadoCuota::create([
                    'nombre' => 'Pendiente',
                    'descripcion' => 'Cuota pendiente de pago',
                    'color' => '#fbbf24',
                    'activo' => true
                ]);
            }

            // Crear la cuota inicial por defecto
            CronogramaCuotaInicial::create([
                'separacion_id' => $separacion->id,
                'proforma_id' => $proforma->id,
                'fecha_pago' => now()->addMonth(), // Un mes después de la separación
                'monto' => $montoTotal,
                'tipo' => 'Cuota Inicial', // Valor por defecto para el campo tipo
                'tipo_cuota_id' => $tipoCuotaInicial->id,
                'estado_id' => $estadoPendiente->id,
                'observaciones' => 'Cuota inicial generada automáticamente',
                'created_by' => Auth::id() ?? 1,
                'updated_by' => Auth::id() ?? 1,
            ]);

            Log::info('Cronograma por defecto creado', [
                'separacion_id' => $separacion->id,
                'monto_total' => $montoTotal,
                'user_id' => Auth::id()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al crear cronograma por defecto: ' . $e->getMessage(), [
                'separacion_id' => $separacion->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        // Agregar parámetro de reload para forzar actualización de datos
        return PanelSeguimientoResource::getUrl('index') . '?reload=' . time();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id() ?? 1;
        $data['updated_by'] = Auth::id() ?? 1;

        // Asegurar que inmuebles_seleccionados sea un array
        if (isset($data['inmuebles_seleccionados'])) {
            $raw = $data['inmuebles_seleccionados'];

            if (is_string($raw)) {
                // Intentar JSON primero
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $data['inmuebles_seleccionados'] = $decoded;
                } else {
                    // Intentar CSV ("1,2,3")
                    $csvParts = preg_split('/\s*,\s*/', trim($raw));
                    $data['inmuebles_seleccionados'] = is_array($csvParts) ? $csvParts : [];
                }
            }

            // Normalizar a enteros independientemente de la forma del arreglo
            if (is_array($data['inmuebles_seleccionados'])) {
                $data['inmuebles_seleccionados'] = array_values(array_filter(array_map(function($item) {
                    // Aceptar formas: 5, "5", [5], {departamento_id: 5}
                    if (is_array($item)) {
                        $val = $item['departamento_id'] ?? (isset($item[0]) ? $item[0] : null);
                        return is_null($val) ? null : (int) $val;
                    }
                    if (is_object($item)) {
                        $val = $item->departamento_id ?? null;
                        return is_null($val) ? null : (int) $val;
                    }
                    return (int) $item;
                }, $data['inmuebles_seleccionados']), function($v) {
                    return !is_null($v);
                }));
            }

            // Si queda vacío, asegurar arreglo vacío
            if (empty($data['inmuebles_seleccionados'])) {
                $data['inmuebles_seleccionados'] = [];
            }
        } else {
            $data['inmuebles_seleccionados'] = [];
        }

        return $data;
    }

    protected function getFormActions(): array
    {
        // Verificar si viene desde separación definitiva y si ya existe una separación para la proforma
        $fromSeparacionDefinitiva = request('from') === 'separacion_definitiva';
        $proformaId = request('proforma_id');

        // Si viene desde separación definitiva y ya existe una separación para esta proforma
        if ($fromSeparacionDefinitiva && $proformaId) {
            $separacionExistente = \App\Models\Separacion::where('proforma_id', $proformaId)->exists();

            if ($separacionExistente) {
                // Mostrar solo el botón de "Guardar sin Separación" para ir al panel de seguimiento
                return [
                    Actions\Action::make('guardar_sin_separacion')
                        ->label('Guardar')
                        ->color('success')
                        ->icon('heroicon-o-check')
                        ->url(PanelSeguimientoResource::getUrl('index'))
                ];
            }
        }

        // Si no hay conflicto, mostrar los botones normales
        return parent::getFormActions();
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Resources\Separaciones\Widgets\CronogramaModalWidget::class,
            \App\Filament\Resources\Separaciones\Widgets\CronogramaSFModalWidget::class,
            \App\Filament\Resources\Separaciones\Widgets\PagoSeparacionModalWidget::class,
        ];
    }
}
