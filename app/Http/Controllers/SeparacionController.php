<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Separacion;
use App\Models\Proforma;
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
                'cliente_data.numero_documento' => 'required|string',
            ]);

            DB::beginTransaction();

            $propiedades = $request->propiedades;
            $clienteData = $request->cliente_data;
            $separacionesCreadas = [];
            $proformasCreadas = [];

            // Obtener el estado de separación para departamentos
            $estadoSeparacion = EstadoDepartamento::where('nombre', 'Separacion')->first();

            foreach ($propiedades as $propiedad) {
                // Obtener el departamento para asegurar actualización de estado en cualquier rama
                $departamento = Departamento::find($propiedad['departamento_id']);
                if (!$departamento) {
                    throw new \Exception("Departamento no encontrado con ID: " . $propiedad['departamento_id']);
                }

                // Verificar si ya existe una proforma para este departamento y cliente
                $proformaExistente = Proforma::where('departamento_id', $propiedad['departamento_id'])
                    ->where('numero_documento', $clienteData['numero_documento'])
                    ->first();

                if ($proformaExistente) {
                    // Usar la proforma existente
                    $proforma = $proformaExistente;
                    
                    // Actualizar los montos si es necesario
                    $proforma->update([
                        'precio_venta' => $propiedad['precio_venta'],
                        'monto_separacion' => $propiedad['monto_separacion'],
                        'monto_cuota_inicial' => $propiedad['monto_cuota_inicial'],
                        'descuento' => $propiedad['precio_lista'] - $propiedad['precio_venta'],
                    ]);
                } else {
                    // Crear nueva proforma para esta propiedad
                    // Obtener el proyecto_id desde el departamento
                    $proyectoId = $departamento->edificio->proyecto_id ?? null;
                    
                    if (!$proyectoId) {
                        throw new \Exception("No se pudo obtener el proyecto_id para el departamento ID: " . $propiedad['departamento_id']);
                    }
                    
                    $proforma = Proforma::create([
                        'departamento_id' => $propiedad['departamento_id'],
                        'proyecto_id' => $proyectoId,
                        'nombres' => $clienteData['nombres'],
                        'ape_paterno' => $clienteData['ape_paterno'],
                        'ape_materno' => $clienteData['ape_materno'] ?? '',
                        'numero_documento' => $clienteData['numero_documento'],
                        'email' => $clienteData['email'] ?? '',
                        'telefono' => $clienteData['telefono'] ?? '',
                        'precio_venta' => $propiedad['precio_venta'],
                        'monto_separacion' => $propiedad['monto_separacion'],
                        'monto_cuota_inicial' => $propiedad['monto_cuota_inicial'],
                        'descuento' => $propiedad['precio_lista'] - $propiedad['precio_venta'],
                        'created_by' => Auth::id() ?? 1,
                        'updated_by' => Auth::id() ?? 1,
                    ]);
                }

                $proformasCreadas[] = $proforma;

                // Verificar si ya existe una separación para esta proforma
                $separacionExistente = Separacion::where('proforma_id', $proforma->id)->first();

                if (!$separacionExistente) {
                    // Crear nueva separación
                    $separacion = Separacion::create([
                        'proforma_id' => $proforma->id,
                        'saldo_a_financiar' => $propiedad['saldo_financiar'],
                        'fecha_vencimiento' => now()->addDays(30), // 30 días por defecto
                        'created_by' => Auth::id() ?? 1,
                        'updated_by' => Auth::id() ?? 1,
                    ]);

                    // Registrar la separación junto con el departamento seleccionado
                    $separacionesCreadas[] = [
                        'model' => $separacion,
                        'departamento_id' => $propiedad['departamento_id']
                    ];

                    Log::info('Separación creada para propiedad', [
                        'separacion_id' => $separacion->id,
                        'proforma_id' => $proforma->id,
                        'departamento_id' => $propiedad['departamento_id'],
                        'monto_separacion' => $propiedad['monto_separacion']
                    ]);
                } else {
                    // Registrar la separación existente junto con el departamento seleccionado
                    $separacionesCreadas[] = [
                        'model' => $separacionExistente,
                        'departamento_id' => $propiedad['departamento_id']
                    ];
                    
                    Log::info('Separación existente encontrada para propiedad', [
                        'separacion_id' => $separacionExistente->id,
                        'proforma_id' => $proforma->id,
                        'departamento_id' => $propiedad['departamento_id']
                    ]);
                }

                // Cambiar el estado del departamento a 'Separacion'
                if ($estadoSeparacion && $departamento) {
                    Log::info('Actualizando estado a Separacion para departamento (múltiple)', [
                        'departamento_id' => $departamento->id,
                        'estado_departamento_id' => $estadoSeparacion->id,
                        'proforma_id' => $proforma->id
                    ]);

                    $departamento->update([
                        'estado_departamento_id' => $estadoSeparacion->id,
                    ]);

                    Log::info('Departamento actualizado a estado Separacion (múltiple)', [
                        'departamento_id' => $departamento->id,
                        'estado_departamento_id' => $estadoSeparacion->id
                    ]);
                }
            }

            DB::commit();

            // Calcular totales
            $totalSeparacion = array_sum(array_column($propiedades, 'monto_separacion'));
            $totalCuotaInicial = array_sum(array_column($propiedades, 'monto_cuota_inicial'));
            $totalSaldoFinanciar = array_sum(array_column($propiedades, 'saldo_financiar'));

                    return response()->json([
                'success' => true,
                'message' => 'Separaciones creadas exitosamente para ' . count($propiedades) . ' propiedades',
                'data' => [
                    'separaciones_creadas' => count($separacionesCreadas),
                    'proformas_procesadas' => count($proformasCreadas),
                    'totales' => [
                        'monto_separacion' => $totalSeparacion,
                        'monto_cuota_inicial' => $totalCuotaInicial,
                        'saldo_a_financiar' => $totalSaldoFinanciar
                    ],
                    'separaciones' => collect($separacionesCreadas)->map(function($item) {
                        $sep = $item['model'];
                        // Usar exactamente el departamento_id del inmueble seleccionado
                        $departamentoId = $item['departamento_id'];

                        return [
                            'id' => $sep->id,
                            'proforma_id' => $sep->proforma_id,
                            'departamento_id' => $departamentoId,
                            'monto_separacion' => $sep->proforma->monto_separacion ?? 0,
                            'departamento' => $sep->proforma->departamento->num_departamento ?? 'N/A',
                            'proyecto' => $sep->proforma->departamento->proyecto->nombre ?? 'N/A'
                        ];
                    })
                ]
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error al crear separaciones múltiples', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear las separaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener información consolidada de múltiples separaciones
     */
    public function getMultipleSeparacionesInfo(Request $request)
    {
        try {
            $separacionIds = $request->input('separacion_ids', []);
            
            if (empty($separacionIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se proporcionaron IDs de separaciones'
                ], 400);
            }

            $separaciones = DB::table('separaciones as s')
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
                ->whereIn('s.id', $separacionIds)
                ->get();

            if ($separaciones->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron separaciones'
                ], 404);
            }

            // Calcular totales
            $totalSeparacion = $separaciones->sum('monto_separacion');
            $totalCuotaInicial = $separaciones->sum('monto_cuota_inicial');
            $totalSaldoFinanciar = $separaciones->sum('saldo_a_financiar');

            // Obtener datos del primer cliente (asumiendo que todas las separaciones son del mismo cliente)
            $primerSeparacion = $separaciones->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'cliente' => [
                        'nombres' => $primerSeparacion->nombres,
                        'apellidos' => trim($primerSeparacion->ape_paterno . ' ' . $primerSeparacion->ape_materno),
                        'numero_documento' => $primerSeparacion->numero_documento
                    ],
                    'totales' => [
                        'monto_separacion' => $totalSeparacion,
                        'monto_cuota_inicial' => $totalCuotaInicial,
                        'saldo_a_financiar' => $totalSaldoFinanciar,
                        'cantidad_propiedades' => $separaciones->count()
                    ],
                    'propiedades' => $separaciones->map(function($sep) {
                        return [
                            'separacion_id' => $sep->separacion_id,
                            'proforma_id' => $sep->proforma_id,
                            'proyecto' => $sep->proyecto_nombre,
                            'departamento' => $sep->num_departamento,
                            'monto_separacion' => $sep->monto_separacion,
                            'monto_cuota_inicial' => $sep->monto_cuota_inicial,
                            'saldo_a_financiar' => $sep->saldo_a_financiar
                        ];
                    })
                ],
                'message' => 'Información de separaciones múltiples obtenida exitosamente'
            ]);

        } catch (Exception $e) {
            Log::error('Error al obtener información de separaciones múltiples', [
                'error' => $e->getMessage(),
                'separacion_ids' => $request->input('separacion_ids', [])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información de separaciones: ' . $e->getMessage()
            ], 500);
        }
    }
}