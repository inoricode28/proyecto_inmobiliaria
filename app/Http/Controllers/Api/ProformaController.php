<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proforma;
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

            $proforma = Proforma::with([
                'departamento.proyecto', 
                'departamento.tipoInmueble', 
                'departamento.separaciones.proforma',
                'inmuebles.departamento.proyecto', 
                'inmuebles.departamento.tipoInmueble',
                'inmuebles.departamento.separaciones.proforma'
            ])->find($proformaId);

            if (!$proforma) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proforma no encontrada'
                ], 404);
            }

            $propiedades = [];

            // Si tiene inmuebles múltiples, usar esos
            if ($proforma->inmuebles && $proforma->inmuebles->count() > 0) {
                foreach ($proforma->inmuebles as $inmueble) {
                    $departamento = $inmueble->departamento;
                    
                    // Verificar si tiene separación existente
                    $separacionExistente = $departamento->separaciones()
                        ->whereHas('proforma', function($query) use ($proforma) {
                            $query->where('numero_documento', $proforma->numero_documento);
                        })
                        ->first();

                    // Usar el descuento directamente de la tabla proforma_inmuebles (ya es porcentaje)
                    $precioLista = $inmueble->precio_lista ?? $departamento->Precio_lista ?? 0;
                    $descuentoPorcentaje = $inmueble->descuento ?? 0;

                    $propiedades[] = [
                        'id' => $inmueble->id ?? $departamento->id,
                        'proyecto' => $departamento->proyecto->nombre ?? 'N/A',
                        'numero' => $departamento->num_departamento ?? 'N/A',
                        'tipo' => $departamento->tipoInmueble->nombre ?? '',
                        'dormitorios' => $departamento->num_dormitorios ?? 0,
                        'area' => $departamento->construida ?? 0,
                        'precio' => $precioLista,
                        'descuento' => $descuentoPorcentaje,
                        'separacion' => $inmueble->monto_separacion ?? 0,
                        'cuota_inicial' => $inmueble->monto_cuota_inicial ?? 0,
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
                    $precioLista = $inmueblePrincipal->precio_lista ?? $departamento->Precio_lista ?? 0;
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