<?php

namespace App\Http\Controllers;

use App\Models\Proforma;
use App\Models\Separacion;
use Illuminate\Http\Request;

class DetalleSeparacionController extends Controller
{
    public function show($proforma_id)
    {
        // Buscar la proforma con todas las relaciones necesarias
        $proforma = Proforma::with([
            'separacion',
            'departamento.estadoDepartamento',
            'departamento.tipoFinanciamiento',
            'departamento.proyecto',
            'prospecto',
            'tipoDocumento',
            'genero',
            'estadoCivil',
            'gradoEstudio',
            'nacionalidad',
            'separacion.notariaKardex',
            'separacion.cartaFianza'
        ])->find($proforma_id);
        
        if (!$proforma) {
            abort(404, 'No se encontró la proforma especificada');
        }
        
        // Permitir acceso sin importar si tiene separación o no
        $departamento = $proforma->departamento;
        $separacion = $proforma->separacion; // Puede ser null

        return view('detalle-separacion', compact('departamento', 'separacion', 'proforma'));
    }
}