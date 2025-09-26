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
}