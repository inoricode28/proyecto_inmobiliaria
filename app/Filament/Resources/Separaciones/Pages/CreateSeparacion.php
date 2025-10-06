<?php

namespace App\Filament\Resources\Separaciones\Pages;

use App\Models\Separacion;
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
                // Pre-llenar el formulario con la proforma encontrada y cargar todos los datos automáticamente
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
                    'precio_lista' => $proforma->departamento->Precio_lista ?? null, // Precio del inmueble
                    'precio_venta' => $proforma->precio_venta ?? null, // Precio de la proforma
                    'descuento' => $proforma->descuento,
                    'departamento_id' => $proforma->departamento_id, // Agregar departamento_id para el select
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

        // Cambiar el estado de los departamentos seleccionados a 'Separacion' y marcar como no vendibles
        if ($separacion->proforma) {
            $estadoSeparacion = EstadoDepartamento::where('nombre', 'Separacion')->first();

            if ($estadoSeparacion) {
                // Manejar múltiples inmuebles asociados a la proforma
                $inmuebles = [];
                if (method_exists($separacion->proforma, 'proformaInmuebles') && $separacion->proforma->proformaInmuebles()->exists()) {
                    $inmuebles = $separacion->proforma->proformaInmuebles()->with('departamento')->get()->pluck('departamento')->filter();
                } elseif ($separacion->proforma->departamento) {
                    $inmuebles = collect([$separacion->proforma->departamento]);
                }

                Log::info('Actualizando estado a Separacion para departamentos de proforma', [
                    'proforma_id' => $separacion->proforma->id,
                    'departamentos_count' => $inmuebles instanceof \Illuminate\Support\Collection ? $inmuebles->count() : (is_array($inmuebles) ? count($inmuebles) : 0)
                ]);

                foreach ($inmuebles as $departamento) {
                    if ($departamento) {
                        $departamento->update([
                            'estado_departamento_id' => $estadoSeparacion->id,
                        ]);

                        Log::info('Departamento actualizado a estado Separacion', [
                            'departamento_id' => $departamento->id ?? null,
                            'estado_departamento_id' => $estadoSeparacion->id,
                            'proforma_id' => $separacion->proforma->id,
                        ]);
                    }
                }
            }
        }

        // Actualizar el estado del prospecto si la proforma tiene un prospecto asociado
        // O si viene desde separación definitiva y hay una proforma
        if ($separacion->proforma && 
            ($separacion->proforma->prospecto_id || $this->data['from_separacion_definitiva'] ?? false)) {
            
            // Si hay prospecto asociado, actualizarlo
            if ($separacion->proforma->prospecto) {
                $separacion->proforma->prospecto->update([
                    'tipo_gestion_id' => 6,
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
                }
            }
        }

        // Crear cronograma de cuotas por defecto si hay datos de la proforma
        $this->crearCronogramaPorDefecto($separacion);

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
        $data['created_by'] = 1;
        $data['updated_by'] = 1;

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