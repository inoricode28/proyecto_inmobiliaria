<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
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
     * Descargar archivo comprobante
     */
    public function downloadComprobante($id)
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