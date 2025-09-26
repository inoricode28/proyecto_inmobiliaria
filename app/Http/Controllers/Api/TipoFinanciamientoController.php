<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoFinanciamiento;
use Illuminate\Http\JsonResponse;

class TipoFinanciamientoController extends Controller
{
    /**
     * Obtiene la lista de tipos de financiamiento para los selects del cronograma SF
     */
    public function index(): JsonResponse
    {
        try {
            $tipos = TipoFinanciamiento::orderBy('nombre')->get(['id', 'nombre', 'descripcion']);
            
            return response()->json([
                'success' => true,
                'data' => $tipos,
                'message' => 'Tipos de financiamiento obtenidos correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Error al obtener los tipos de financiamiento: ' . $e->getMessage()
            ], 500);
        }
    }
}