<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;

class DetalleSeparacionController extends Controller
{
    public function show(Departamento $departamento)
    {
        // Cargar todas las relaciones necesarias
        $departamento->load([
            'estadoDepartamento',
            'tipoFinanciamiento',
            'proyecto',
            'separaciones.proforma.prospecto',
            'separaciones.proforma.tipoDocumento',
            'separaciones.proforma.genero',
            'separaciones.proforma.estadoCivil',
            'separaciones.proforma.gradoEstudio',
            'separaciones.proforma.nacionalidad',
            // 'separaciones.visitas', // Eliminar esta línea
            'separaciones.notariaKardex',
            'separaciones.cartaFianza'
        ]);

        // Obtener la separación más reciente
        $separacion = $departamento->separaciones()->latest()->first();
        
        if (!$separacion) {
            abort(404, 'No se encontró información de separación para este departamento');
        }

        return view('detalle-separacion', compact('departamento', 'separacion'));
    }
}