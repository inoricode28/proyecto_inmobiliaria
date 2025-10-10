<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Separacion;
use App\Models\SeparacionInmueble;
use App\Models\Proforma;
use App\Models\ProformaInmueble;
use App\Models\Departamento;
use App\Models\EstadoDepartamento;
use Exception;

class SeparacionController extends Controller
{
    /**
     * Crear separación para múltiples propiedades
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada
            $request->validate([
                'propiedades' => 'required|array|min:1',
                'propiedades.*.departamento_id' => 'required|exists:departamentos,id',
                'propiedades.*.precio_lista' => 'required|numeric|min:0',
                'propiedades.*.precio_venta' => 'required|numeric|min:0',
                'propiedades.*.monto_separacion' => 'required|numeric|min:0',
                'propiedades.*.monto_cuota_inicial' => 'required|numeric|min:0',
                'propiedades.*.saldo_financiar' => 'required|numeric|min:0',
                'cliente_data' => 'required|array',
                'cliente_data.nombres' => 'required|string',
                'cliente_data.ape_paterno' => 'required|string',
                'cliente_data.numero_documento' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $propiedades = $request->propiedades;
            $clienteData = $request->cliente_data;

            // LOG ESPECÍFICO: Ver qué datos está recibiendo el controlador
            Log::info('=== DATOS RECIBIDOS EN CONTROLADOR ===', [
                'total_propiedades' => count($propiedades),
                'propiedades_ids' => array_column($propiedades, 'departamento_id'),
                'propiedades_completas' => $propiedades,
                'cliente_data' => $clienteData
            ]);

            // Obtener el estado de separación para departamentos
            $estadoSeparacion = EstadoDepartamento::where('nombre', 'Separacion')->first();

            // Crear una proforma principal con el primer inmueble
            $primerPropiedad = $propiedades[0];
            $departamentoPrincipal = Departamento::find($primerPropiedad['departamento_id']);
            
            if (!$departamentoPrincipal) {
                throw new \Exception("Departamento principal no encontrado con ID: " . $primerPropiedad['departamento_id']);
            }

            // Obtener el proyecto_id desde el departamento principal
            $proyectoId = $departamentoPrincipal->edificio->proyecto_id ?? null;
            
            if (!$proyectoId) {
                throw new \Exception("No se pudo obtener el proyecto_id para el departamento ID: " . $primerPropiedad['departamento_id']);
            }

            // Calcular totales
            $totalSeparacion = array_sum(array_column($propiedades, 'monto_separacion'));
            $totalCuotaInicial = array_sum(array_column($propiedades, 'monto_cuota_inicial'));
            $totalSaldoFinanciar = array_sum(array_column($propiedades, 'saldo_financiar'));
            $totalPrecioVenta = array_sum(array_column($propiedades, 'precio_venta'));
            $totalPrecioLista = array_sum(array_column($propiedades, 'precio_lista'));

            // Crear la proforma principal
            $proforma = Proforma::create([
                'departamento_id' => $primerPropiedad['departamento_id'],
                'proyecto_id' => $proyectoId,
                'nombres' => $clienteData['nombres'],
                'ape_paterno' => $clienteData['ape_paterno'],
                'ape_materno' => $clienteData['ape_materno'] ?? '',
                'numero_documento' => $clienteData['numero_documento'],
                'email' => $clienteData['email'] ?? '',
                'telefono' => $clienteData['telefono'] ?? '',
                'precio_venta' => $totalPrecioVenta,
                'monto_separacion' => $totalSeparacion,
                'monto_cuota_inicial' => $totalCuotaInicial,
                'descuento' => $totalPrecioLista - $totalPrecioVenta,
                'created_by' => Auth::id() ?? 1,
                'updated_by' => Auth::id() ?? 1,
            ]);

            // Crear registros en proforma_inmuebles para todos los inmuebles
            foreach ($propiedades as $index => $propiedad) {
                $departamento = Departamento::find($propiedad['departamento_id']);
                
                if (!$departamento) {
                    throw new \Exception("Departamento no encontrado con ID: " . $propiedad['departamento_id']);
                }

                // Obtener precio_lista del departamento si no se proporciona
                $precioLista = $propiedad['precio_lista'] ?? $departamento->Precio_lista ?? 0;
                
                // Calcular descuento como porcentaje
                $descuentoPorcentaje = 0;
                if ($precioLista > 0) {
                    $descuentoPorcentaje = (($precioLista - $propiedad['precio_venta']) / $precioLista) * 100;
                }

                ProformaInmueble::create([
                    'proforma_id' => $proforma->id,
                    'departamento_id' => $propiedad['departamento_id'],
                    'precio_lista' => $precioLista,
                    'precio_venta' => $propiedad['precio_venta'],
                    'descuento' => $descuentoPorcentaje,
                    'orden' => $index + 1,
                    'created_by' => Auth::id() ?? 1,
                    'updated_by' => Auth::id() ?? 1,
                ]);

                Log::info('ProformaInmueble creado', [
                    'proforma_id' => $proforma->id,
                    'departamento_id' => $propiedad['departamento_id'],
                    'precio_lista' => $precioLista,
                    'precio_venta' => $propiedad['precio_venta'],
                    'orden' => $index + 1
                ]);
            }

            // Crear la separación
            $separacion = Separacion::create([
                'proforma_id' => $proforma->id,
                'saldo_a_financiar' => $totalSaldoFinanciar,
                'fecha_vencimiento' => now()->addDays(30), // 30 días por defecto
                'created_by' => Auth::id() ?? 1,
                'updated_by' => Auth::id() ?? 1,
            ]);

            // Crear registros en separacion_inmuebles para todos los inmuebles
            Log::info('=== INICIANDO CREACIÓN DE SEPARACION_INMUEBLES ===', [
                'separacion_id' => $separacion->id,
                'total_propiedades_a_procesar' => count($propiedades)
            ]);

            foreach ($propiedades as $index => $propiedad) {
                $departamento = Departamento::find($propiedad['departamento_id']);
                
                Log::info('=== PROCESANDO DEPARTAMENTO ===', [
                    'index' => $index,
                    'departamento_id' => $propiedad['departamento_id'],
                    'departamento_encontrado' => $departamento ? true : false,
                    'num_departamento' => $departamento ? $departamento->num_departamento : 'N/A',
                    'separacion_id' => $separacion->id
                ]);
                
                $separacionInmueble = SeparacionInmueble::create([
                    'separacion_id' => $separacion->id,
                    'departamento_id' => $propiedad['departamento_id'],
                    'precio_lista' => $propiedad['precio_lista'] ?? $departamento->Precio_lista ?? 0,
                    'precio_venta' => $propiedad['precio_venta'],
                    'monto_separacion' => $propiedad['monto_separacion'],
                    'monto_cuota_inicial' => $propiedad['monto_cuota_inicial'],
                    'saldo_financiar' => $propiedad['saldo_financiar'],
                    'orden' => $index + 1,
                    'created_by' => Auth::id() ?? 1,
                    'updated_by' => Auth::id() ?? 1,
                ]);

                Log::info('=== SEPARACION_INMUEBLE CREADO ===', [
                    'separacion_inmueble_id' => $separacionInmueble->id,
                    'separacion_id' => $separacion->id,
                    'departamento_id' => $propiedad['departamento_id'],
                    'num_departamento' => $departamento ? $departamento->num_departamento : 'N/A',
                    'orden' => $index + 1
                ]);

                // Cambiar el estado del departamento a 'Separacion'
                if ($estadoSeparacion && $departamento) {
                    Log::info('Actualizando estado a Separacion para departamento', [
                        'departamento_id' => $departamento->id,
                        'estado_departamento_id' => $estadoSeparacion->id,
                        'separacion_id' => $separacion->id
                    ]);

                    $departamento->update([
                        'estado_departamento_id' => $estadoSeparacion->id,
                    ]);

                    Log::info('Departamento actualizado a estado Separacion', [
                        'departamento_id' => $departamento->id,
                        'estado_departamento_id' => $estadoSeparacion->id
                    ]);
                }
            }

            DB::commit();

            Log::info('Separación múltiple creada exitosamente', [
                'separacion_id' => $separacion->id,
                'proforma_id' => $proforma->id,
                'cantidad_inmuebles' => count($propiedades),
                'total_separacion' => $totalSeparacion,
                'total_cuota_inicial' => $totalCuotaInicial,
                'total_saldo_financiar' => $totalSaldoFinanciar
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Separación creada exitosamente para ' . count($propiedades) . ' propiedades',
                'data' => [
                    'separacion_id' => $separacion->id,
                    'proforma_id' => $proforma->id,
                    'cantidad_inmuebles' => count($propiedades),
                    'totales' => [
                        'monto_separacion' => $totalSeparacion,
                        'monto_cuota_inicial' => $totalCuotaInicial,
                        'saldo_a_financiar' => $totalSaldoFinanciar,
                        'precio_venta_total' => $totalPrecioVenta,
                        'precio_lista_total' => $totalPrecioLista
                    ],
                    'inmuebles' => collect($propiedades)->map(function($propiedad, $index) use ($separacion) {
                        $departamento = Departamento::find($propiedad['departamento_id']);
                        return [
                            'departamento_id' => $propiedad['departamento_id'],
                            'departamento' => $departamento->num_departamento ?? 'N/A',
                            'proyecto' => $departamento->proyecto->nombre ?? 'N/A',
                            'precio_lista' => $propiedad['precio_lista'],
                            'precio_venta' => $propiedad['precio_venta'],
                            'monto_separacion' => $propiedad['monto_separacion'],
                            'orden' => $index + 1
                        ];
                    })
                ]
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error al crear separación múltiple', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear la separación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener información de una separación con múltiples inmuebles
     */
    public function getSeparacionInfo($separacionId)
    {
        try {
            // Obtener información básica de la separación
            $separacion = DB::table('separaciones as s')
                ->leftJoin('proformas as p', 's.proforma_id', '=', 'p.id')
                ->select(
                    's.id as separacion_id',
                    's.proforma_id',
                    's.saldo_a_financiar',
                    's.fecha_vencimiento',
                    'p.monto_separacion',
                    'p.monto_cuota_inicial',
                    'p.nombres',
                    'p.ape_paterno',
                    'p.ape_materno',
                    'p.numero_documento',
                    'p.email',
                    'p.telefono',
                    'p.precio_venta as precio_venta_total'
                )
                ->where('s.id', $separacionId)
                ->first();

            if (!$separacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Separación no encontrada'
                ], 404);
            }

            // Obtener todos los inmuebles de la separación
            $inmuebles = DB::table('separacion_inmuebles as si')
                ->leftJoin('departamentos as d', 'si.departamento_id', '=', 'd.id')
                ->leftJoin('proyectos as pr', 'd.proyecto_id', '=', 'pr.id')
                ->select(
                    'si.id as separacion_inmueble_id',
                    'si.departamento_id',
                    'si.precio_lista',
                    'si.precio_venta',
                    'si.monto_separacion',
                    'si.monto_cuota_inicial',
                    'si.saldo_financiar',
                    'si.orden',
                    'd.num_departamento',
                    'pr.nombre as proyecto_nombre'
                )
                ->where('si.separacion_id', $separacionId)
                ->orderBy('si.orden')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'separacion' => [
                        'id' => $separacion->separacion_id,
                        'proforma_id' => $separacion->proforma_id,
                        'saldo_a_financiar' => $separacion->saldo_a_financiar,
                        'fecha_vencimiento' => $separacion->fecha_vencimiento
                    ],
                    'cliente' => [
                        'nombres' => $separacion->nombres,
                        'apellidos' => trim($separacion->ape_paterno . ' ' . $separacion->ape_materno),
                        'numero_documento' => $separacion->numero_documento,
                        'email' => $separacion->email,
                        'telefono' => $separacion->telefono
                    ],
                    'totales' => [
                        'monto_separacion' => $separacion->monto_separacion,
                        'monto_cuota_inicial' => $separacion->monto_cuota_inicial,
                        'saldo_a_financiar' => $separacion->saldo_a_financiar,
                        'precio_venta_total' => $separacion->precio_venta_total,
                        'cantidad_inmuebles' => $inmuebles->count()
                    ],
                    'inmuebles' => $inmuebles->map(function($inmueble) {
                        return [
                            'separacion_inmueble_id' => $inmueble->separacion_inmueble_id,
                            'departamento_id' => $inmueble->departamento_id,
                            'proyecto' => $inmueble->proyecto_nombre,
                            'departamento' => $inmueble->num_departamento,
                            'precio_lista' => $inmueble->precio_lista,
                            'precio_venta' => $inmueble->precio_venta,
                            'monto_separacion' => $inmueble->monto_separacion,
                            'monto_cuota_inicial' => $inmueble->monto_cuota_inicial,
                            'saldo_financiar' => $inmueble->saldo_financiar,
                            'orden' => $inmueble->orden
                        ];
                    })
                ],
                'message' => 'Información de separación obtenida exitosamente'
            ]);

        } catch (Exception $e) {
            Log::error('Error al obtener información de separación', [
                'error' => $e->getMessage(),
                'separacion_id' => $separacionId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información de separación: ' . $e->getMessage()
            ], 500);
        }
    }
}