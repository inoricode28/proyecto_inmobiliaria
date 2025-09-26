<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banco;
use Illuminate\Http\JsonResponse;

class BancoController extends Controller
{
    /**
     * Obtiene la lista de bancos para los selects del cronograma SF
     */
    public function index(): JsonResponse
    {
        try {
            $bancos = Banco::orderBy('nombre')->get(['id', 'nombre']);
            
            return response()->json([
                'success' => true,
                'data' => $bancos,
                'message' => 'Bancos obtenidos correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Error al obtener los bancos: ' . $e->getMessage()
            ], 500);
        }
    }
}