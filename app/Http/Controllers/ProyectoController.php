<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\EstadoProyecto;
use App\Models\Empresa;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $proyectos = Proyecto::with(['estado', 'empresa', 'edificios'])
                            ->orderBy('nombre')
                            ->get();
                            
        return view('proyectos.index', compact('proyectos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $estados = EstadoProyecto::orderBy('nombre')->get();
        $empresas = Empresa::orderBy('nombre')->get();
        
        return view('proyectos.create', compact('estados', 'empresas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_entrega' => 'required|date|after_or_equal:fecha_inicio',
            'estado_proyecto_id' => 'required|exists:estados_proyecto,id',
            'empresa_constructora_id' => 'required|exists:empresas,id'
        ]);

        Proyecto::create($validated);

        return redirect()->route('proyectos.index')
                         ->with('success', 'Proyecto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $proyecto = Proyecto::with(['estado', 'empresa', 'edificios'])
                          ->findOrFail($id);
                          
        return view('proyectos.show', compact('proyecto'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $proyecto = Proyecto::findOrFail($id);
        $estados = EstadoProyecto::orderBy('nombre')->get();
        $empresas = Empresa::orderBy('nombre')->get();
        
        return view('proyectos.edit', compact('proyecto', 'estados', 'empresas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $proyecto = Proyecto::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_entrega' => 'required|date|after_or_equal:fecha_inicio',
            'estado_proyecto_id' => 'required|exists:estados_proyecto,id',
            'empresa_constructora_id' => 'required|exists:empresas,id'
        ]);

        $proyecto->update($validated);

        return redirect()->route('proyectos.index')
                         ->with('success', 'Proyecto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $proyecto = Proyecto::findOrFail($id);
        
        if($proyecto->edificios()->count() > 0) {
            return redirect()->route('proyectos.index')
                             ->with('error', 'No se puede eliminar el proyecto porque tiene edificios asociados.');
        }
        
        $proyecto->delete();

        return redirect()->route('proyectos.index')
                         ->with('success', 'Proyecto eliminado exitosamente.');
    }
}