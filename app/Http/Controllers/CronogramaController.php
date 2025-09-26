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

            // Si no hay separacion_id, guardar cuotas temporales asociadas a la proforma
            if (!$separacionId) {
                // Eliminar cuotas temporales existentes para esta proforma
                CronogramaCuotaInicial::where('proforma_id', $proformaId)
                    ->whereNull('separacion_id')
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
            // Buscar si ya existe una separación para esta proforma
            $separacionExistente = Separacion::where('proforma_id', $proformaId)->first();
            
            if ($separacionExistente) {
                $separacion = $separacionExistente;
            } else {
                // Crear nueva separación
                $separacion = Separacion::create([
                    'proforma_id' => $proformaId,
                    'cliente_id' => $clienteId,
                    'fecha_separacion' => now(),
                    'estado' => 'Activo',
                    'created_by' => Auth::id() ?? 1,
                    'updated_by' => Auth::id() ?? 1,
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

            // Eliminar cuotas existentes para esta separación (si las hay)
            CronogramaCuotaInicial::where('separacion_id', $separacionId)->delete();

            // También eliminar cuotas temporales de esta proforma
            CronogramaCuotaInicial::where('proforma_id', $proformaId)
                ->whereNull('separacion_id')
                ->delete();

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
     * Guardar cronograma de saldo a financiar
     */
    public function guardarCronogramaSF(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'proforma_id' => 'required|exists:proformas,id',
                'separacion_id' => 'required|exists:separaciones,id',
                'entidad_financiera_id' => 'required|exists:bancos,id',
                'tipo_financiamiento_id' => 'required|exists:tipos_financiamiento,id',
                'tipo_comprobante' => 'nullable|string|max:50',
                'bono_mi_vivienda' => 'boolean',
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

            // Crear el cronograma principal
            $cronogramaSF = \App\Models\CronogramaSaldoFinanciar::create([
                'separacion_id' => $request->separacion_id,
                'entidad_financiera_id' => $request->entidad_financiera_id,
                'tipo_financiamiento_id' => $request->tipo_financiamiento_id,
                'tipo_comprobante' => $request->tipo_comprobante,
                'bono_mi_vivienda' => $request->bono_mi_vivienda ?? false,
                'bono_verde' => $request->bono_verde ?? false,
                'bono_integrador' => $request->bono_integrador ?? false,
                'estado' => 'Activo',
                'created_by' => Auth::id() ?? 1,
                'updated_by' => Auth::id() ?? 1,
            ]);

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
}