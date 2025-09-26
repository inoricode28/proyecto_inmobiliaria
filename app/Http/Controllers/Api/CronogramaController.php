<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoCuota;
use App\Models\EstadoCuota;
use App\Models\TipoComprobante;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CronogramaController extends Controller
{
    /**
     * Obtener tipos de cuota disponibles
     */
    public function getTiposCuota(): JsonResponse
    {
        try {
            $tipos = TipoCuota::activos()
                ->select('id', 'nombre', 'descripcion')
                ->orderBy('nombre')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $tipos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener tipos de cuota',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estados de cuota disponibles
     */
    public function getEstadosCuota(): JsonResponse
    {
        try {
            $estados = EstadoCuota::activos()
                ->select('id', 'nombre', 'descripcion', 'color')
                ->orderBy('nombre')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $estados
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estados de cuota',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener tipos de comprobante
     */
    public function getTiposComprobante(): JsonResponse
    {
        try {
            $tiposComprobante = TipoComprobante::activos()
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