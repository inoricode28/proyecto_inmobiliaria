<?php

namespace App\Http\Controllers;

use App\Models\CronogramaCuotaInicial;
use App\Models\Separacion;
use App\Models\TipoCuota;
use App\Models\EstadoCuota;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CronogramaController extends Controller
{
    /**
     * Guardar cronograma de cuotas y crear separación si no existe
     */
    public function guardarCronograma(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'cuotas' => 'required|array|min:1',
                'cuotas.*.fecha_pago' => 'required|date',
                'cuotas.*.monto' => 'required|numeric|min:0',
                'cuotas.*.tipo_cuota_id' => 'required|exists:tipos_cuota,id',
                // Campos para crear separación si no existe
                'proforma_id' => 'required|exists:proformas,id',
                'cliente_id' => 'nullable|exists:clientes,id',
            ]);

            $cuotas = $request->cuotas;
            $proformaId = $request->proforma_id;
            $clienteId = $request->cliente_id;
            $separacionId = $request->separacion_id;

            DB::beginTransaction();

            // Obtener el estado por defecto para las cuotas (pendiente)
            $estadoPendiente = EstadoCuota::where('nombre', 'Pendiente')->first();
            if (!$estadoPendiente) {
                // Si no existe, crear el estado pendiente
                $estadoPendiente = EstadoCuota::create([
                    'nombre' => 'Pendiente',
                    'descripcion' => 'Cuota pendiente de pago',
                    'color' => '#fbbf24',
                    'activo' => true
                ]);
            }

            // SIEMPRE verificar si ya existe una separación para esta proforma
            $separacionExistente = Separacion::where('proforma_id', $proformaId)->first();
            
            // Si no hay separacion_id en el request PERO existe una separación para la proforma
            if (!$separacionId && $separacionExistente) {
                $separacionId = $separacionExistente->id;
                Log::info('Separación existente encontrada para proforma', [
                    'proforma_id' => $proformaId,
                    'separacion_id' => $separacionId
                ]);
            }

            // Si no hay separacion_id Y no existe separación, guardar cuotas temporales
            if (!$separacionId) {
                // Eliminar TODAS las cuotas existentes para esta proforma (temporales y definitivas)
                CronogramaCuotaInicial::where('proforma_id', $proformaId)
                    ->delete();

                // Crear las nuevas cuotas temporales
                foreach ($cuotas as $cuotaData) {
                    CronogramaCuotaInicial::create([
                        'proforma_id' => $proformaId,
                        'separacion_id' => null, // Cuotas temporales
                        'fecha_pago' => $cuotaData['fecha_pago'],
                        'monto' => $cuotaData['monto'],
                        'tipo' => 'Cuota Inicial',
                        'tipo_cuota_id' => $cuotaData['tipo_cuota_id'],
                        'estado_id' => $estadoPendiente->id,
                        'created_by' => Auth::id() ?? 1,
                        'updated_by' => Auth::id() ?? 1,
                    ]);
                }

                DB::commit();

                Log::info('Cuotas temporales guardadas exitosamente', [
                    'proforma_id' => $proformaId,
                    'cuotas_count' => count($cuotas),
                    'user_id' => Auth::id()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Cronograma guardado exitosamente (Cuotas temporales)',
                    'data' => [
                        'proforma_id' => $proformaId,
                        'cuotas_guardadas' => count($cuotas),
                        'temporal' => true
                    ]
                ]);
            }

            // Si hay separacion_id, proceder con el flujo normal
            // SIEMPRE usar la separación existente si hay una para esta proforma
            if ($separacionExistente) {
                $separacion = $separacionExistente;
                Log::info('Usando separación existente para la proforma', [
                    'proforma_id' => $proformaId,
                    'separacion_id' => $separacionExistente->id,
                    'separacion_fecha' => $separacionExistente->created_at
                ]);
            } else {
                // Solo crear nueva separación si NO existe una para esta proforma
                $separacion = Separacion::create([
                    'proforma_id' => $proformaId,
                    'cliente_id' => $clienteId,
                    'fecha_separacion' => now(),
                    'estado' => 'Activo',
                    'created_by' => Auth::id() ?? 1,
                    'updated_by' => Auth::id() ?? 1,
                ]);

                Log::info('Nueva separación creada para la proforma', [
                    'proforma_id' => $proformaId,
                    'separacion_id' => $separacion->id
                ]);

                // Cambiar el estado del prospecto a separación (tipo_gestion_id = 6)
                $proforma = \App\Models\Proforma::find($proformaId);
                if ($proforma && $proforma->prospecto) {
                    $proforma->prospecto->update([
                        'tipo_gestion_id' => 6,
                    ]);
                }
            }
            
            $separacionId = $separacion->id;

            // Verificar que la separación existe
            $separacion = Separacion::find($separacionId);
            if (!$separacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Separación no encontrada'
                ], 404);
            }

            // Cambiar el estado del prospecto a separación (tipo_gestion_id = 6) si no está ya en ese estado
            if ($separacion->proforma && $separacion->proforma->prospecto) {
                $prospecto = $separacion->proforma->prospecto;
                if ($prospecto->tipo_gestion_id !== 6) {
                    $prospecto->update([
                        'tipo_gestion_id' => 6,
                    ]);
                    Log::info('Estado del prospecto actualizado a separación', [
                        'prospecto_id' => $prospecto->id,
                        'separacion_id' => $separacionId,
                        'tipo_gestion_anterior' => $prospecto->tipo_gestion_id,
                        'tipo_gestion_nuevo' => 6
                    ]);
                }
            }

            // Eliminar TODAS las cuotas existentes para esta proforma (definitivas y temporales)
            CronogramaCuotaInicial::where('proforma_id', $proformaId)->delete();

            // Crear las nuevas cuotas
            foreach ($cuotas as $cuotaData) {
                CronogramaCuotaInicial::create([
                    'separacion_id' => $separacionId,
                    'proforma_id' => $proformaId,
                    'fecha_pago' => $cuotaData['fecha_pago'],
                    'monto' => $cuotaData['monto'],
                    'tipo' => 'Cuota Inicial', // Valor por defecto para el campo tipo
                    'tipo_cuota_id' => $cuotaData['tipo_cuota_id'],
                    'estado_id' => $estadoPendiente->id,
                    'created_by' => Auth::id() ?? 1,
                    'updated_by' => Auth::id() ?? 1,
                ]);
            }

            DB::commit();

            Log::info('Cronograma guardado exitosamente', [
                'separacion_id' => $separacionId,
                'cuotas_count' => count($cuotas),
                'user_id' => Auth::id(),
                'nueva_separacion' => !$request->separacion_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cronograma guardado exitosamente',
                'data' => [
                    'separacion_id' => $separacionId,
                    'cuotas_guardadas' => count($cuotas),
                    'separacion_creada' => !$request->separacion_id
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar cronograma', [
                'error' => $e->getMessage(),
                'separacion_id' => $request->separacion_id ?? null,
                'proforma_id' => $request->proforma_id ?? null,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener cronograma de una separación
     */
    public function obtenerCronograma($separacionId): JsonResponse
    {
        try {
            $cuotas = CronogramaCuotaInicial::with(['tipoCuota', 'estadoCuota'])
                ->where('separacion_id', $separacionId)
                ->orderBy('fecha_pago')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $cuotas
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener cronograma', [
                'error' => $e->getMessage(),
                'separacion_id' => $separacionId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el cronograma'
            ], 500);
        }
    }

    /**
     * Obtener cuotas temporales por proforma_id
     */
    public function obtenerCuotasTemporales($proformaId): JsonResponse
    {
        try {
            $cuotas = CronogramaCuotaInicial::with(['tipoCuota', 'estadoCuota'])
                ->where('proforma_id', $proformaId)
                ->whereNull('separacion_id')
                ->orderBy('fecha_pago')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $cuotas
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener cuotas temporales', [
                'error' => $e->getMessage(),
                'proforma_id' => $proformaId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las cuotas temporales'
            ], 500);
        }
    }

    /**
     * Obtener cuotas definitivas por proforma_id (cuotas que ya tienen separacion_id)
     */
    public function obtenerCuotasDefinitivasPorProforma($proformaId): JsonResponse
    {
        try {
            $cuotas = CronogramaCuotaInicial::with(['tipoCuota', 'estadoCuota'])
                ->where('proforma_id', $proformaId)
                ->whereNotNull('separacion_id')
                ->orderBy('fecha_pago')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $cuotas
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener cuotas definitivas por proforma', [
                'error' => $e->getMessage(),
                'proforma_id' => $proformaId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las cuotas definitivas'
            ], 500);
        }
    }

    /**
     * Obtener cronograma de saldo a financiar por separación
     */
    public function obtenerCronogramaSF($separacion_id): JsonResponse
    {
        try {
            $cronogramasSF = \App\Models\CronogramaSaldoFinanciar::where('separacion_id', $separacion_id)
                ->with(['detalles' => function($query) {
                    $query->orderBy('numero_cuota');
                }, 'banco', 'tipoFinanciamiento'])
                ->get();

            $cuotas = [];
            foreach ($cronogramasSF as $cronogramaSF) {
                foreach ($cronogramaSF->detalles as $detalle) {
                    $cuotas[] = [
                        'id' => $detalle->id,
                        'cronograma_sf_id' => $cronogramaSF->id,
                        'numero_cuota' => $detalle->numero_cuota,
                        'fecha_pago' => $detalle->fecha_pago,
                        'monto' => $detalle->monto,
                        'motivo' => $detalle->motivo,
                        'estado' => $detalle->estado,
                        'observaciones' => $detalle->observaciones,
                        'entidad_financiera' => $cronogramaSF->banco->nombre ?? 'N/A',
                        'tipo_financiamiento' => $cronogramaSF->tipoFinanciamiento->nombre ?? 'N/A',
                        'tipo_comprobante' => $cronogramaSF->tipoComprobante->nombre ?? 'N/A',
                        'tipo_comprobante_id' => $cronogramaSF->tipo_comprobante_id,
                        'bono_mi_vivienda' => $cronogramaSF->bono_mi_vivienda,
                        'bono_verde' => $cronogramaSF->bono_verde,
                        'bono_integrador' => $cronogramaSF->bono_integrador,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $cuotas,
                'message' => 'Cuotas de saldo a financiar obtenidas exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener cronograma SF', [
                'separacion_id' => $separacion_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las cuotas de saldo a financiar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener cuotas de saldo a financiar temporales por proforma
     */
    public function obtenerCuotasSFTemporales($proforma_id): JsonResponse
    {
        try {
            $cronogramasSF = \App\Models\CronogramaSaldoFinanciar::where('proforma_id', $proforma_id)
                ->whereNull('separacion_id')
                ->with(['detalles' => function($query) {
                    $query->orderBy('numero_cuota');
                }, 'banco', 'tipoFinanciamiento'])
                ->get();

            $cuotas = [];
            foreach ($cronogramasSF as $cronogramaSF) {
                foreach ($cronogramaSF->detalles as $detalle) {
                    $cuotas[] = [
                        'id' => $detalle->id,
                        'cronograma_sf_id' => $cronogramaSF->id,
                        'numero_cuota' => $detalle->numero_cuota,
                        'fecha_pago' => $detalle->fecha_pago,
                        'monto' => $detalle->monto,
                        'motivo' => $detalle->motivo,
                        'estado' => $detalle->estado,
                        'observaciones' => $detalle->observaciones,
                        'entidad_financiera' => $cronogramaSF->banco->nombre ?? 'N/A',
                        'tipo_financiamiento' => $cronogramaSF->tipoFinanciamiento->nombre ?? 'N/A',
                        'tipo_comprobante' => $cronogramaSF->tipoComprobante->nombre ?? 'N/A',
                        'tipo_comprobante_id' => $cronogramaSF->tipo_comprobante_id,
                        'bono_mi_vivienda' => $cronogramaSF->bono_mi_vivienda,
                        'bono_verde' => $cronogramaSF->bono_verde,
                        'bono_integrador' => $cronogramaSF->bono_integrador,
                        'es_temporal' => true
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $cuotas,
                'message' => 'Cuotas temporales de saldo a financiar obtenidas exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener cuotas SF temporales', [
                'proforma_id' => $proforma_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las cuotas temporales de saldo a financiar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener cuotas de saldo a financiar definitivas por proforma
     */
    public function obtenerCuotasSFDefinitivasPorProforma($proforma_id): JsonResponse
    {
        try {
            $cronogramasSF = \App\Models\CronogramaSaldoFinanciar::where('proforma_id', $proforma_id)
                ->whereNotNull('separacion_id')
                ->with(['detalles' => function($query) {
                    $query->orderBy('numero_cuota');
                }, 'banco', 'tipoFinanciamiento'])
                ->get();

            $cuotas = [];
            foreach ($cronogramasSF as $cronogramaSF) {
                foreach ($cronogramaSF->detalles as $detalle) {
                    $cuotas[] = [
                        'id' => $detalle->id,
                        'cronograma_sf_id' => $cronogramaSF->id,
                        'numero_cuota' => $detalle->numero_cuota,
                        'fecha_pago' => $detalle->fecha_pago,
                        'monto' => $detalle->monto,
                        'motivo' => $detalle->motivo,
                        'estado' => $detalle->estado,
                        'observaciones' => $detalle->observaciones,
                        'entidad_financiera' => $cronogramaSF->banco->nombre ?? 'N/A',
                        'tipo_financiamiento' => $cronogramaSF->tipoFinanciamiento->nombre ?? 'N/A',
                        'tipo_comprobante' => $cronogramaSF->tipoComprobante->nombre ?? 'N/A',
                        'bono_mi_vivienda' => $cronogramaSF->bono_mi_vivienda,
                        'bono_verde' => $cronogramaSF->bono_verde,
                        'bono_integrador' => $cronogramaSF->bono_integrador,
                        'es_definitiva' => true
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $cuotas,
                'message' => 'Cuotas definitivas de saldo a financiar obtenidas exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener cuotas SF definitivas por proforma', [
                'proforma_id' => $proforma_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las cuotas definitivas de saldo a financiar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guardar cronograma de saldo a financiar
     */
    public function guardarCronogramaSF(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'proforma_id' => 'required|exists:proformas,id',
                'separacion_id' => 'nullable|exists:separaciones,id',
                'fecha_inicio' => 'required|date',
                'monto_total' => 'required|numeric|min:0',
                'saldo_financiar' => 'required|numeric|min:0',
                'numero_cuotas' => 'required|integer|min:1',
                'banco_id' => 'required|exists:bancos,id',
                'tipo_financiamiento_id' => 'required|exists:tipos_financiamiento,id',
                'tipo_comprobante_id' => 'nullable|exists:tipos_comprobante,id',
                'bono_mivivienda' => 'boolean',
                'bono_verde' => 'boolean',
                'bono_integrador' => 'boolean',
                'cuotas' => 'required|array|min:1',
                'cuotas.*.numero_cuota' => 'required|integer|min:1',
                'cuotas.*.fecha_pago' => 'required|date',
                'cuotas.*.monto' => 'required|numeric|min:0',
                'cuotas.*.motivo' => 'required|string|max:255',
                'cuotas.*.estado' => 'required|string|max:50',
            ]);

            DB::beginTransaction();

            // Verificar si ya existe una separación para esta proforma
            $separacionExistente = null;
            if ($request->proforma_id) {
                $separacionExistente = \App\Models\Separacion::where('proforma_id', $request->proforma_id)->first();
                
                if ($separacionExistente) {
                    Log::info('Separación existente encontrada para cronograma SF', [
                        'proforma_id' => $request->proforma_id,
                        'separacion_id' => $separacionExistente->id
                    ]);
                    
                    // Usar la separación existente
                    $separacionId = $separacionExistente->id;
                } else {
                    // Usar el separacion_id proporcionado o null si no existe separación
                    $separacionId = $request->separacion_id;
                    
                    Log::info('No se encontró separación existente para cronograma SF', [
                        'proforma_id' => $request->proforma_id,
                        'separacion_id_provided' => $request->separacion_id
                    ]);
                }
            } else {
                $separacionId = $request->separacion_id;
            }

            // Buscar cronograma SF existente para esta proforma
            $cronogramaSFExistente = \App\Models\CronogramaSaldoFinanciar::where('proforma_id', $request->proforma_id)->first();
            
            if ($cronogramaSFExistente) {
                // Actualizar el cronograma existente
                $cronogramaSFExistente->update([
                    'separacion_id' => $separacionId, // Usar la separación existente o la proporcionada
                    'fecha_inicio' => $request->fecha_inicio,
                    'monto_total' => $request->monto_total,
                    'saldo_financiar' => $request->saldo_financiar,
                    'numero_cuotas' => $request->numero_cuotas,
                    'banco_id' => $request->banco_id,
                    'tipo_financiamiento_id' => $request->tipo_financiamiento_id,
                    'tipo_comprobante_id' => $request->tipo_comprobante_id,
                    'bono_mivivienda' => $request->bono_mivivienda ?? false,
                    'bono_verde' => $request->bono_verde ?? false,
                    'bono_integrador' => $request->bono_integrador ?? false,
                    'estado' => 'Activo',
                    'updated_by' => Auth::id() ?? 1,
                ]);

                // Eliminar solo los detalles existentes para reemplazarlos
                \App\Models\CronogramaSaldoFinanciarDetalle::where('cronograma_sf_id', $cronogramaSFExistente->id)->delete();
                
                $cronogramaSF = $cronogramaSFExistente;
                
                Log::info('Cronograma SF existente actualizado', [
                    'cronograma_sf_id' => $cronogramaSF->id,
                    'proforma_id' => $request->proforma_id
                ]);
            } else {
                // Crear nuevo cronograma si no existe
                $cronogramaSF = \App\Models\CronogramaSaldoFinanciar::create([
                    'separacion_id' => $separacionId, // Usar la separación existente o la proporcionada
                    'proforma_id' => $request->proforma_id,
                    'fecha_inicio' => $request->fecha_inicio,
                    'monto_total' => $request->monto_total,
                    'saldo_financiar' => $request->saldo_financiar,
                    'numero_cuotas' => $request->numero_cuotas,
                    'banco_id' => $request->banco_id,
                    'tipo_financiamiento_id' => $request->tipo_financiamiento_id,
                    'tipo_comprobante_id' => $request->tipo_comprobante_id,
                    'bono_mivivienda' => $request->bono_mivivienda ?? false,
                    'bono_verde' => $request->bono_verde ?? false,
                    'bono_integrador' => $request->bono_integrador ?? false,
                    'estado' => 'Activo',
                    'created_by' => Auth::id() ?? 1,
                    'updated_by' => Auth::id() ?? 1,
                ]);
                
                Log::info('Nuevo cronograma SF creado', [
                    'cronograma_sf_id' => $cronogramaSF->id,
                    'proforma_id' => $request->proforma_id
                ]);
            }

            // Crear los detalles del cronograma
            foreach ($request->cuotas as $cuotaData) {
                \App\Models\CronogramaSaldoFinanciarDetalle::create([
                    'cronograma_sf_id' => $cronogramaSF->id,
                    'numero_cuota' => $cuotaData['numero_cuota'],
                    'fecha_pago' => $cuotaData['fecha_pago'],
                    'monto' => $cuotaData['monto'],
                    'motivo' => $cuotaData['motivo'],
                    'estado' => $cuotaData['estado'],
                    'observaciones' => $cuotaData['observaciones'] ?? null,
                ]);
            }

            DB::commit();

            Log::info('Cronograma SF guardado exitosamente', [
                'cronograma_sf_id' => $cronogramaSF->id,
                'separacion_id' => $request->separacion_id,
                'total_cuotas' => count($request->cuotas),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cronograma de Saldo a Financiar guardado exitosamente',
                'data' => [
                    'cronograma_sf_id' => $cronogramaSF->id,
                    'total_cuotas' => count($request->cuotas)
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Datos de validación incorrectos',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al guardar cronograma SF', [
                'error' => $e->getMessage(),
                'proforma_id' => $request->proforma_id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el cronograma de saldo a financiar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener tipos de comprobante
     */
    public function getTiposComprobante(): JsonResponse
    {
        try {
            $tiposComprobante = \App\Models\TipoComprobante::activos()
                ->select('id', 'nombre', 'descripcion')
                ->orderBy('nombre')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $tiposComprobante
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener tipos de comprobante', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener tipos de comprobante'
            ], 500);
        }
    }
}