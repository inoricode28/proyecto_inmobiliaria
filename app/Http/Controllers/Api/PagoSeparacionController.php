<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Moneda;
use App\Models\MedioPago;
use App\Models\CuentaBancaria;
use Exception;

class PagoSeparacionController extends Controller
{
    /**
     * Obtener todos los pagos de una separación
     */
    public function index($separacionId)
    {
        try {
            $pagos = DB::table('pagos_separacion as ps')
                ->leftJoin('moneda as m', 'ps.moneda_id', '=', 'm.id')
                ->leftJoin('medios_pago as mp', 'ps.medio_pago_id', '=', 'mp.id')
                ->leftJoin('cuentas_bancarias as cb', 'ps.cuenta_bancaria_id', '=', 'cb.id')
                ->leftJoin('users as u', 'ps.registrado_por', '=', 'u.id')
                ->select(
                    'ps.*',
                    'm.nombre as moneda_nombre',
                    'mp.nombre as medio_pago_nombre',
                    'cb.banco',
                    'cb.numero_cuenta',
                    'u.name as registrado_por_nombre'
                )
                ->where('ps.separacion_id', $separacionId)
                ->orderBy('ps.fecha_pago', 'desc')
                ->get();

            // Formatear los datos para el frontend
            $pagosFormatted = $pagos->map(function ($pago) {
                return [
                    'id' => $pago->id,
                    'separacion_id' => $pago->separacion_id,
                    'fecha_pago' => $pago->fecha_pago,
                    'monto' => $pago->monto,
                    'tipo_cambio' => $pago->tipo_cambio,
                    'monto_pago' => $pago->monto_pago,
                    'numero_operacion' => $pago->numero_operacion,
                    'numero_documento' => $pago->numero_documento,
                    'agencia_bancaria' => $pago->agencia_bancaria,
                    'archivo_comprobante' => $pago->archivo_comprobante,
                    'observaciones' => $pago->observaciones,
                    'moneda' => [
                        'id' => $pago->moneda_id,
                        'nombre' => $pago->moneda_nombre
                    ],
                    'medio_pago' => [
                        'id' => $pago->medio_pago_id,
                        'nombre' => $pago->medio_pago_nombre
                    ],
                    'cuenta_bancaria' => $pago->cuenta_bancaria_id ? [
                        'id' => $pago->cuenta_bancaria_id,
                        'banco' => $pago->banco,
                        'numero_cuenta' => $pago->numero_cuenta
                    ] : null,
                    'registrado_por' => [
                        'id' => $pago->registrado_por,
                        'nombre' => $pago->registrado_por_nombre
                    ],
                    'created_at' => $pago->created_at,
                    'updated_at' => $pago->updated_at
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $pagosFormatted,
                'message' => 'Pagos obtenidos exitosamente'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los pagos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todos los pagos de una proforma (incluyendo pagos con separacion_id NULL)
     */
    public function getByProforma($proformaId)
    {
        try {
            // Obtener información de la proforma para calcular el saldo pendiente
            $proforma = DB::table('proformas')
                ->select('monto_separacion')
                ->where('id', $proformaId)
                ->first();

            if (!$proforma) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proforma no encontrada'
                ], 404);
            }

            // Calcular el monto total de la proforma (separación + cuota inicial)
            $montoTotalProforma = ($proforma->monto_separacion ?? 0);

            $pagos = DB::table('pagos_separacion as ps')
                ->leftJoin('moneda as m', 'ps.moneda_id', '=', 'm.id')
                ->leftJoin('medios_pago as mp', 'ps.medio_pago_id', '=', 'mp.id')
                ->leftJoin('cuentas_bancarias as cb', 'ps.cuenta_bancaria_id', '=', 'cb.id')
                ->leftJoin('users as u', 'ps.registrado_por', '=', 'u.id')
                ->select(
                    'ps.*',
                    'm.nombre as moneda_nombre',
                    'mp.nombre as medio_pago_nombre',
                    'cb.banco',
                    'cb.numero_cuenta',
                    'u.name as registrado_por_nombre'
                )
                ->where('ps.proforma_id', $proformaId)
                ->orderBy('ps.fecha_pago', 'desc')
                ->get();

            // Calcular la suma total de pagos realizados
            $totalPagosRealizados = $pagos->sum('monto_pago');

            // Calcular el saldo pendiente
            $saldoPendiente = $montoTotalProforma - $totalPagosRealizados;

            // Formatear los datos para el frontend
            $pagosFormatted = $pagos->map(function ($pago) {
                return [
                    'id' => $pago->id,
                    'separacion_id' => $pago->separacion_id,
                    'proforma_id' => $pago->proforma_id,
                    'fecha_pago' => $pago->fecha_pago,
                    'monto' => $pago->monto,
                    'tipo_cambio' => $pago->tipo_cambio,
                    'monto_pago' => $pago->monto_pago,
                    'numero_operacion' => $pago->numero_operacion,
                    'numero_documento' => $pago->numero_documento,
                    'agencia_bancaria' => $pago->agencia_bancaria,
                    'archivo_comprobante' => $pago->archivo_comprobante,
                    'observaciones' => $pago->observaciones,
                    'moneda' => [
                        'id' => $pago->moneda_id,
                        'nombre' => $pago->moneda_nombre
                    ],
                    'medio_pago' => [
                        'id' => $pago->medio_pago_id,
                        'nombre' => $pago->medio_pago_nombre
                    ],
                    'cuenta_bancaria' => $pago->cuenta_bancaria_id ? [
                        'id' => $pago->cuenta_bancaria_id,
                        'banco' => $pago->banco,
                        'numero_cuenta' => $pago->numero_cuenta
                    ] : null,
                    'registrado_por' => [
                        'id' => $pago->registrado_por,
                        'nombre' => $pago->registrado_por_nombre
                    ],
                    'created_at' => $pago->created_at,
                    'updated_at' => $pago->updated_at
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $pagosFormatted,
                'resumen' => [
                    'monto_total_proforma' => $montoTotalProforma,
                    'total_pagos_realizados' => $totalPagosRealizados,
                    'saldo_pendiente' => $saldoPendiente,
                    'monto_separacion' => $proforma->monto_separacion ?? 0,
                    'monto_cuota_inicial' => $proforma->monto_cuota_inicial ?? 0
                ],
                'message' => 'Pagos obtenidos exitosamente'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los pagos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear múltiples pagos de separación en lote
     */
    public function storeBatch(Request $request)
    {
        try {
            // Validar los datos de entrada
            $validator = Validator::make($request->all(), [
                'proforma_id' => 'required|integer|exists:proformas,id',
                'separacion_id' => 'nullable|integer|exists:separaciones,id',
                'pagos' => 'required|string' // JSON string de pagos
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Decodificar los pagos del JSON
            $pagosData = json_decode($request->pagos, true);
            if (!$pagosData || !is_array($pagosData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Formato de pagos inválido'
                ], 422);
            }

            $proformaId = $request->proforma_id;
            $separacionId = $request->separacion_id;

            // Obtener el monto de separación
            $montoSeparacion = DB::table('proformas')
                ->where('id', $proformaId)
                ->value('monto_separacion');

            if (!$montoSeparacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proforma no encontrada o sin monto de separación'
                ], 404);
            }

            // Si no se proporciona separacion_id, intentar encontrarlo
            if (!$separacionId) {
                $separacion = DB::table('separaciones')
                    ->where('proforma_id', $proformaId)
                    ->first();
                
                if ($separacion) {
                    $separacionId = $separacion->id;
                    Log::info('Separación encontrada automáticamente:', [
                        'proforma_id' => $proformaId,
                        'separacion_id' => $separacionId
                    ]);
                } else {
                    Log::warning('No se encontró separación para la proforma:', [
                        'proforma_id' => $proformaId,
                        'separacion_id' => $separacionId
                    ]);
                }
            }

            // Obtener la suma de pagos existentes
            $pagosExistentes = DB::table('pagos_separacion')
                ->where('proforma_id', $proformaId)
                ->sum('monto_pago');

            // Log para depuración
            Log::info('Datos de pagos recibidos:', [
                'pagos' => $pagosData,
                'monto_separacion' => $montoSeparacion,
                'pagos_existentes' => $pagosExistentes
            ]);

            // Validar que la suma de montos (existentes + nuevos) no exceda el monto de separación
            $totalNuevosPagos = array_sum(array_column($pagosData, 'monto_pago'));
            $totalTodosPagos = $pagosExistentes + $totalNuevosPagos;
            
            Log::info('Cálculo de totales:', [
                'total_nuevos_pagos' => $totalNuevosPagos,
                'total_todos_pagos' => $totalTodosPagos,
                'montos_individuales' => array_column($pagosData, 'monto_pago')
            ]);
            
            if ($totalTodosPagos > $montoSeparacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'La suma total de pagos (S/ ' . number_format($totalTodosPagos, 2) . ') no puede exceder el monto de separación (S/ ' . number_format($montoSeparacion, 2) . '). Pagos existentes: S/ ' . number_format($pagosExistentes, 2) . ', Nuevos pagos: S/ ' . number_format($totalNuevosPagos, 2)
                ], 422);
            }

            DB::beginTransaction();

            $pagosCreados = [];
            $archivosSubidos = [];
            $userId = auth()->id() ?? 1;

            // Crear cada pago
            foreach ($pagosData as $index => $pagoData) {
                // Procesar archivo si existe
                $archivoPath = null;
                $archivoKey = "archivo_comprobante_{$index}";
                
                if ($request->hasFile($archivoKey)) {
                    $archivo = $request->file($archivoKey);
                    
                    // Validar archivo
                    if (!$archivo->isValid()) {
                        throw new Exception("Archivo comprobante {$index} no es válido");
                    }
                    
                    // Validar tamaño (5MB máximo)
                    if ($archivo->getSize() > 5 * 1024 * 1024) {
                        throw new Exception("Archivo comprobante {$index} excede el tamaño máximo de 5MB");
                    }
                    
                    // Validar tipo de archivo
                    $allowedMimes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
                    $extension = $archivo->getClientOriginalExtension();
                    if (!in_array(strtolower($extension), $allowedMimes)) {
                        throw new Exception("Archivo comprobante {$index} tiene un formato no permitido");
                    }
                    
                    // Generar nombre único y guardar archivo
                    $nombreArchivo = time() . '_' . $index . '_' . $archivo->getClientOriginalName();
                    $archivoPath = $archivo->storeAs('pagos_separacion', $nombreArchivo, 'public');
                    $archivosSubidos[] = $archivoPath;
                    
                    Log::info("Archivo subido:", [
                        'index' => $index,
                        'nombre_original' => $archivo->getClientOriginalName(),
                        'ruta_guardada' => $archivoPath,
                        'tamaño' => $archivo->getSize()
                    ]);
                }

                $pagoId = DB::table('pagos_separacion')->insertGetId([
                    'separacion_id' => $separacionId,
                    'proforma_id' => $proformaId,
                    'fecha_pago' => $pagoData['fecha_pago'],
                    'monto' => $pagoData['monto'],
                    'tipo_cambio' => $pagoData['tipo_cambio'],
                    'monto_pago' => $pagoData['monto_pago'],
                    'monto_convertido' => $pagoData['monto_convertido'],
                    'moneda_id' => $pagoData['moneda_id'],
                    'medio_pago_id' => $pagoData['medio_pago_id'],
                    'cuenta_bancaria_id' => $pagoData['cuenta_bancaria_id'] ?? null,
                    'numero_operacion' => $pagoData['numero_operacion'] ?? null,
                    'numero_documento' => $pagoData['numero_documento'] ?? null,
                    'agencia_bancaria' => $pagoData['agencia_bancaria'] ?? null,
                    'archivo_comprobante' => $archivoPath, // Ahora se guarda la ruta del archivo
                    'observaciones' => $pagoData['observaciones'] ?? null,
                    'registrado_por' => $userId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $pagosCreados[] = $pagoId;
            }

            DB::commit();

            // Obtener los pagos creados con sus relaciones
            $pagos = DB::table('pagos_separacion as ps')
                ->leftJoin('moneda as m', 'ps.moneda_id', '=', 'm.id')
                ->leftJoin('medios_pago as mp', 'ps.medio_pago_id', '=', 'mp.id')
                ->leftJoin('cuentas_bancarias as cb', 'ps.cuenta_bancaria_id', '=', 'cb.id')
                ->leftJoin('users as u', 'ps.registrado_por', '=', 'u.id')
                ->select(
                    'ps.*',
                    'm.nombre as moneda_nombre',
                    'mp.nombre as medio_pago_nombre',
                    'cb.banco',
                    'cb.numero_cuenta',
                    'u.name as registrado_por_nombre'
                )
                ->whereIn('ps.id', $pagosCreados)
                ->orderBy('ps.fecha_pago', 'desc')
                ->get();

            // Formatear los datos para el frontend
            $pagosFormatted = $pagos->map(function ($pago) {
                return [
                    'id' => $pago->id,
                    'separacion_id' => $pago->separacion_id,
                    'fecha_pago' => $pago->fecha_pago,
                    'monto' => $pago->monto,
                    'tipo_cambio' => $pago->tipo_cambio,
                    'monto_pago' => $pago->monto_pago,
                    'numero_operacion' => $pago->numero_operacion,
                    'numero_documento' => $pago->numero_documento,
                    'agencia_bancaria' => $pago->agencia_bancaria,
                    'archivo_comprobante' => $pago->archivo_comprobante,
                    'observaciones' => $pago->observaciones,
                    'moneda' => [
                        'id' => $pago->moneda_id,
                        'nombre' => $pago->moneda_nombre
                    ],
                    'medio_pago' => [
                        'id' => $pago->medio_pago_id,
                        'nombre' => $pago->medio_pago_nombre
                    ],
                    'cuenta_bancaria' => $pago->cuenta_bancaria_id ? [
                        'id' => $pago->cuenta_bancaria_id,
                        'banco' => $pago->banco,
                        'numero_cuenta' => $pago->numero_cuenta
                    ] : null,
                    'registrado_por' => [
                        'id' => $pago->registrado_por,
                        'nombre' => $pago->registrado_por_nombre
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $pagosFormatted,
                'message' => 'Pagos registrados exitosamente'
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            
            // Eliminar archivos subidos si falló la transacción
            foreach ($archivosSubidos as $archivoPath) {
                if (Storage::disk('public')->exists($archivoPath)) {
                    Storage::disk('public')->delete($archivoPath);
                }
            }

            Log::error('Error en storeBatch:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar los pagos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear un nuevo pago de separación
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada
            $validator = Validator::make($request->all(), [
                'separacion_id' => 'required|integer|exists:separaciones,id',
                'fecha_pago' => 'required|date',
                'monto' => 'required|numeric|min:0',
                'tipo_cambio' => 'required|numeric|min:0',
                'monto_pago' => 'required|numeric|min:0',
                'moneda_id' => 'required|integer|exists:moneda,id',
                'medio_pago_id' => 'required|integer|exists:medios_pago,id',
                'cuenta_bancaria_id' => 'nullable|integer|exists:cuentas_bancarias,id',
                'numero_operacion' => 'nullable|string|max:100',
                'numero_documento' => 'nullable|string|max:100',
                'agencia_bancaria' => 'nullable|string|max:200',
                'archivo_comprobante' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // 5MB
                'observaciones' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Obtener información de la separación para validaciones adicionales
            $separacion = DB::table('separaciones as s')
                ->leftJoin('proformas as p', 's.proforma_id', '=', 'p.id')
                ->select('s.id', 's.proforma_id', 'p.monto_separacion')
                ->where('s.id', $request->separacion_id)
                ->first();

            if (!$separacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Separación no encontrada'
                ], 404);
            }

            // Validar que el monto no exceda el monto de separación
            if ($request->monto > $separacion->monto_separacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'El monto no puede exceder el monto de separación (S/ ' . number_format($separacion->monto_separacion, 2) . ')'
                ], 422);
            }

            DB::beginTransaction();

            // Procesar archivo si existe
            $archivoPath = null;
            if ($request->hasFile('archivo_comprobante')) {
                $archivo = $request->file('archivo_comprobante');
                $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
                $archivoPath = $archivo->storeAs('pagos_separacion', $nombreArchivo, 'public');
            }

            // Crear el pago
            $pagoId = DB::table('pagos_separacion')->insertGetId([
                'separacion_id' => $request->separacion_id,
                'proforma_id' => $separacion->proforma_id,
                'fecha_pago' => $request->fecha_pago,
                'monto' => $request->monto,
                'tipo_cambio' => $request->tipo_cambio,
                'monto_pago' => $request->monto_pago,
                'moneda_id' => $request->moneda_id,
                'medio_pago_id' => $request->medio_pago_id,
                'cuenta_bancaria_id' => $request->cuenta_bancaria_id,
                'numero_operacion' => $request->numero_operacion,
                'numero_documento' => $request->numero_documento,
                'agencia_bancaria' => $request->agencia_bancaria,
                'archivo_comprobante' => $archivoPath,
                'observaciones' => $request->observaciones,
                'registrado_por' => auth()->id() ?? 1, // Usuario autenticado o usuario por defecto
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            // Obtener el pago creado con sus relaciones
            $pago = DB::table('pagos_separacion as ps')
                ->leftJoin('moneda as m', 'ps.moneda_id', '=', 'm.id')
                ->leftJoin('medios_pago as mp', 'ps.medio_pago_id', '=', 'mp.id')
                ->leftJoin('cuentas_bancarias as cb', 'ps.cuenta_bancaria_id', '=', 'cb.id')
                ->leftJoin('users as u', 'ps.registrado_por', '=', 'u.id')
                ->select(
                    'ps.*',
                    'm.nombre as moneda_nombre',
                    'mp.nombre as medio_pago_nombre',
                    'cb.banco',
                    'cb.numero_cuenta',
                    'u.name as registrado_por_nombre'
                )
                ->where('ps.id', $pagoId)
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $pago->id,
                    'separacion_id' => $pago->separacion_id,
                    'fecha_pago' => $pago->fecha_pago,
                    'monto' => $pago->monto,
                    'tipo_cambio' => $pago->tipo_cambio,
                    'monto_pago' => $pago->monto_pago,
                    'numero_operacion' => $pago->numero_operacion,
                    'numero_documento' => $pago->numero_documento,
                    'agencia_bancaria' => $pago->agencia_bancaria,
                    'archivo_comprobante' => $pago->archivo_comprobante,
                    'observaciones' => $pago->observaciones,
                    'moneda' => [
                        'id' => $pago->moneda_id,
                        'nombre' => $pago->moneda_nombre
                    ],
                    'medio_pago' => [
                        'id' => $pago->medio_pago_id,
                        'nombre' => $pago->medio_pago_nombre
                    ],
                    'cuenta_bancaria' => $pago->cuenta_bancaria_id ? [
                        'id' => $pago->cuenta_bancaria_id,
                        'banco' => $pago->banco,
                        'numero_cuenta' => $pago->numero_cuenta
                    ] : null,
                    'registrado_por' => [
                        'id' => $pago->registrado_por,
                        'nombre' => $pago->registrado_por_nombre
                    ]
                ],
                'message' => 'Pago registrado exitosamente'
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            
            // Eliminar archivo si se subió pero falló la transacción
            if (isset($archivoPath) && Storage::disk('public')->exists($archivoPath)) {
                Storage::disk('public')->delete($archivoPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un pago de separación
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Obtener el pago antes de eliminarlo para borrar el archivo
            $pago = DB::table('pagos_separacion')->where('id', $id)->first();
            
            if (!$pago) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pago no encontrado'
                ], 404);
            }

            // Eliminar archivo si existe
            if ($pago->archivo_comprobante && Storage::disk('public')->exists($pago->archivo_comprobante)) {
                Storage::disk('public')->delete($pago->archivo_comprobante);
            }

            // Eliminar el pago
            DB::table('pagos_separacion')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pago eliminado exitosamente'
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener información de separación incluyendo monto_separacion
     */
    public function getSeparacionInfo($separacionId)
    {
        try {
            $separacion = DB::table('separaciones as s')
                ->leftJoin('proformas as p', 's.proforma_id', '=', 'p.id')
                ->leftJoin('departamentos as d', 'p.departamento_id', '=', 'd.id')
                ->leftJoin('proyectos as pr', 'd.proyecto_id', '=', 'pr.id')
                ->select(
                    's.id as separacion_id',
                    's.proforma_id',
                    'p.monto_separacion',
                    'p.monto_cuota_inicial',
                    'p.nombres',
                    'p.ape_paterno',
                    'p.ape_materno',
                    'p.numero_documento',
                    'd.num_departamento',
                    'pr.nombre as proyecto_nombre',
                    's.saldo_a_financiar'
                )
                ->where('s.id', $separacionId)
                ->first();

            if (!$separacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Separación no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'separacion_id' => $separacion->separacion_id,
                    'proforma_id' => $separacion->proforma_id,
                    'monto_separacion' => $separacion->monto_separacion,
                    'monto_cuota_inicial' => $separacion->monto_cuota_inicial,
                    'cliente' => [
                        'nombres' => $separacion->nombres,
                        'apellidos' => trim($separacion->ape_paterno . ' ' . $separacion->ape_materno),
                        'numero_documento' => $separacion->numero_documento
                    ],
                    'inmueble' => [
                        'proyecto' => $separacion->proyecto_nombre,
                        'departamento' => $separacion->num_departamento
                    ],
                    'saldo_a_financiar' => $separacion->saldo_a_financiar
                ],
                'message' => 'Información de separación obtenida exitosamente'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información de separación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener monedas disponibles
     */
    public function getMonedas()
    {
        try {
            $monedas = Moneda::select('id', 'nombre')
                ->orderBy('nombre')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $monedas,
                'message' => 'Monedas obtenidas exitosamente'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las monedas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener medios de pago disponibles
     */
    public function getMediosPago()
    {
        try {
            $mediosPago = MedioPago::select('id', 'nombre', 'descripcion')
                ->activos()
                ->orderBy('nombre')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $mediosPago,
                'message' => 'Medios de pago obtenidos exitosamente'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los medios de pago: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener cuentas bancarias disponibles
     */
    public function getCuentasBancarias()
    {
        try {
            $cuentasBancarias = CuentaBancaria::select('id', 'banco', 'numero_cuenta', 'tipo_cuenta', 'moneda', 'titular')
                ->activas()
                ->orderBy('banco')
                ->orderBy('moneda')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $cuentasBancarias,
                'message' => 'Cuentas bancarias obtenidas exitosamente'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las cuentas bancarias: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Descargar comprobante de pago
     */
    public function descargarComprobante($id)
    {
        try {
            $pago = DB::table('pagos_separacion')->where('id', $id)->first();
            
            if (!$pago || !$pago->archivo_comprobante) {
                return response()->json([
                    'success' => false,
                    'message' => 'Archivo no encontrado'
                ], 404);
            }

            if (!Storage::disk('public')->exists($pago->archivo_comprobante)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Archivo no existe en el servidor'
                ], 404);
            }

            return Storage::disk('public')->download($pago->archivo_comprobante);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al descargar el archivo: ' . $e->getMessage()
            ], 500);
        }
    }
}