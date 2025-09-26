<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoCuota;
use App\Models\EstadoCuota;
use Illuminate\Http\JsonResponse;

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
}