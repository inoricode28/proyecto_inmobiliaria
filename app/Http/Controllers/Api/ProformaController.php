<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proforma;
use App\Models\ProformaInmueble;
use Illuminate\Http\Request;

class ProformaController extends Controller
{
    /**
     * Obtener datos de la proforma para el cronograma
     */
    public function getCronogramaData($proformaId)
    {
        try {
            $proforma = Proforma::with(['proyecto', 'departamento', 'separacion'])->find($proformaId);

            if (!$proforma) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proforma no encontrada'
                ], 404);
            }

            // Calcular precio de venta con descuento
            $precioLista = $proforma->departamento->Precio_lista ?? 0;
            $descuento = $proforma->descuento ?? 0;
            $precioVenta = $precioLista - (($descuento * $precioLista) / 100);

            // Obtener separación y cuota inicial
            $montoSeparacion = $proforma->separacion->monto_separacion ?? 0;
            $cuotaInicial = $proforma->monto_cuota_inicial ?? 0;

            // Calcular saldo a financiar: Precio Venta - Separación - Cuota Inicial
            $saldoFinanciar = $precioVenta - $montoSeparacion - $cuotaInicial;

            return response()->json([
                'success' => true,
                'proyecto' => $proforma->proyecto->nombre ?? 'N/A',
                'inmueble' => $proforma->departamento->num_departamento ?? 'N/A',
                'precio_venta' => number_format($saldoFinanciar, 2), // Enviamos el saldo a financiar como precio_venta
                'cuota_inicial' => number_format($cuotaInicial, 2),
                'monto_cuota_inicial' => $cuotaInicial,
                'saldo_financiar' => $saldoFinanciar // Campo adicional para claridad
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos de la proforma: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener propiedades de una proforma con información de separación
     */
    public function getPropiedadesConSeparacion(Request $request)
    {
        try {
            $proformaId = $request->query('proforma_id');

            if (!$proformaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'proforma_id es requerido'
                ], 400);
            }

            // Cargar la proforma y preparar consulta directa a proforma_inmuebles
            $proforma = Proforma::with([
                'departamento.proyecto',
                'departamento.tipoInmueble',
                'departamento.separaciones.proforma'
            ])->find($proformaId);

            if (!$proforma) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proforma no encontrada'
                ], 404);
            }

            $propiedades = [];

            // Consultar directamente los inmuebles asociados a la proforma
            $inmuebles = ProformaInmueble::with([
                'departamento.proyecto',
                'departamento.tipoInmueble',
                'departamento.separaciones.proforma'
            ])->where('proforma_id', $proformaId)->orderBy('orden')->get();

            // Si existen registros en proforma_inmuebles, usar SIEMPRE esos
            if ($inmuebles->count() > 0) {
                foreach ($inmuebles as $pi) {
                    $departamento = $pi->departamento;

                    if (!$departamento) {
                        // Saltar si no hay departamento asociado
                        continue;
                    }

                    // Verificar si tiene separación existente ligada al mismo documento
                    $separacionExistente = $departamento->separaciones()
                        ->whereHas('proforma', function($query) use ($proforma) {
                            $query->where('numero_documento', $proforma->numero_documento);
                        })
                        ->first();

                    // Tomar precio lista del registro proforma_inmuebles; si es 0 o null, hacer fallback
                    $precioLista = 0;
                    if (!is_null($pi->precio_lista) && $pi->precio_lista > 0) {
                        $precioLista = $pi->precio_lista;
                    } elseif (!is_null($departamento->Precio_lista) && $departamento->Precio_lista > 0) {
                        $precioLista = $departamento->Precio_lista;
                    } elseif (!is_null($pi->precio_venta) && $pi->precio_venta > 0 && (($pi->descuento ?? 0) == 0)) {
                        // Si no hay descuento y solo tenemos precio_venta, úsalo como lista
                        $precioLista = $pi->precio_venta;
                    }
                    // El descuento en proforma_inmuebles ya está guardado como porcentaje
                    $descuentoPorcentaje = $pi->descuento ?? 0;

                    $propiedades[] = [
                        // Usar el ID del departamento para coherencia en selección/tabla
                        'id' => $departamento->id,
                        'proyecto' => $departamento->proyecto->nombre ?? 'N/A',
                        'numero' => $departamento->num_departamento ?? 'N/A',
                        'tipo' => $departamento->tipoInmueble->nombre ?? '',
                        'dormitorios' => $departamento->num_dormitorios ?? 0,
                        'area' => $departamento->construida ?? 0,
                        'precio' => $precioLista,
                        'descuento' => $descuentoPorcentaje,
                        'separacion' => $pi->monto_separacion ?? 0,
                        'cuota_inicial' => $pi->monto_cuota_inicial ?? 0,
                        'tiene_separacion' => $separacionExistente ? true : false,
                        'separacion_id' => $separacionExistente ? $separacionExistente->id : null
                    ];
                }
            }
            // Si no hay inmuebles múltiples, buscar el inmueble principal en proforma_inmuebles
            else {
                $inmueblePrincipal = $proforma->inmueblePrincipal;

                if ($inmueblePrincipal && $inmueblePrincipal->departamento) {
                    $departamento = $inmueblePrincipal->departamento;

                    // Verificar si tiene separación existente
                    $separacionExistente = $departamento->separaciones()
                        ->whereHas('proforma', function($query) use ($proforma) {
                            $query->where('numero_documento', $proforma->numero_documento);
                        })
                        ->first();

                    // Usar el descuento directamente de la tabla proforma_inmuebles (ya es porcentaje)
                    // Para precio lista, aplicar el mismo esquema de fallback
                    $precioLista = 0;
                    if (!is_null($inmueblePrincipal->precio_lista) && $inmueblePrincipal->precio_lista > 0) {
                        $precioLista = $inmueblePrincipal->precio_lista;
                    } elseif (!is_null($departamento->Precio_lista) && $departamento->Precio_lista > 0) {
                        $precioLista = $departamento->Precio_lista;
                    } elseif (!is_null($inmueblePrincipal->precio_venta) && $inmueblePrincipal->precio_venta > 0 && (($inmueblePrincipal->descuento ?? 0) == 0)) {
                        $precioLista = $inmueblePrincipal->precio_venta;
                    }
                    $descuentoPorcentaje = $inmueblePrincipal->descuento ?? 0;

                    $propiedades[] = [
                        'id' => $departamento->id,
                        'proyecto' => $departamento->proyecto->nombre ?? 'N/A',
                        'numero' => $departamento->num_departamento ?? 'N/A',
                        'tipo' => $departamento->tipoInmueble->nombre ?? '',
                        'dormitorios' => $departamento->num_dormitorios ?? 0,
                        'area' => $departamento->construida ?? 0,
                        'precio' => $precioLista,
                        'descuento' => $descuentoPorcentaje,
                        'separacion' => $inmueblePrincipal->monto_separacion ?? 0,
                        'cuota_inicial' => $inmueblePrincipal->monto_cuota_inicial ?? 0,
                        'tiene_separacion' => $separacionExistente ? true : false,
                        'separacion_id' => $separacionExistente ? $separacionExistente->id : null
                    ];
                }
                // Fallback: usar el departamento directamente de la proforma (compatibilidad con datos antiguos)
                elseif ($proforma->departamento) {
                    $departamento = $proforma->departamento;

                    // Verificar si tiene separación existente
                    $separacionExistente = $departamento->separaciones()
                        ->whereHas('proforma', function($query) use ($proforma) {
                            $query->where('numero_documento', $proforma->numero_documento);
                        })
                        ->first();

                    // Calcular descuento como porcentaje del precio lista (fallback para datos antiguos)
                    $precioLista = $departamento->Precio_lista ?? 0;
                    $descuentoAbsoluto = $proforma->descuento ?? 0;
                    $descuentoPorcentaje = $precioLista > 0 ? ($descuentoAbsoluto / $precioLista) * 100 : 0;

                    $propiedades[] = [
                        'id' => $departamento->id,
                        'proyecto' => $departamento->proyecto->nombre ?? 'N/A',
                        'numero' => $departamento->num_departamento ?? 'N/A',
                        'tipo' => $departamento->tipoInmueble->nombre ?? '',
                        'dormitorios' => $departamento->num_dormitorios ?? 0,
                        'area' => $departamento->construida ?? 0,
                        'precio' => $precioLista,
                        'descuento' => $descuentoPorcentaje,
                        'separacion' => $proforma->monto_separacion ?? 0,
                        'cuota_inicial' => $proforma->monto_cuota_inicial ?? 0,
                        'tiene_separacion' => $separacionExistente ? true : false,
                        'separacion_id' => $separacionExistente ? $separacionExistente->id : null
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'propiedades' => $propiedades,
                'message' => 'Propiedades obtenidas exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener propiedades: ' . $e->getMessage()
            ], 500);
        }
    }
}
