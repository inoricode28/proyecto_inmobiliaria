<?php

namespace App\Http\Controllers;

use App\Models\EstadoProyecto;
use Illuminate\Http\Request;

class EstadoProyectoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $estados = EstadoProyecto::withCount('proyectos')->orderBy('nombre')->get();
        return view('estados_proyecto.index', compact('estados'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('estados_proyecto.create');
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
            'nombre' => 'required|string|max:50|unique:estados_proyecto,nombre',
            'descripcion' => 'nullable|string|max:255'
        ]);

        EstadoProyecto::create($validated);

        return redirect()->route('estados-proyecto.index')
                         ->with('success', 'Estado de proyecto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $estado = EstadoProyecto::with(['proyectos' => function($query) {
            $query->orderBy('nombre');
        }])->findOrFail($id);
        
        return view('estados_proyecto.show', compact('estado'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $estado = EstadoProyecto::findOrFail($id);
        return view('estados_proyecto.edit', compact('estado'));
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
        $estado = EstadoProyecto::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:estados_proyecto,nombre,'.$estado->id,
            'descripcion' => 'nullable|string|max:255'
        ]);

        $estado->update($validated);

        return redirect()->route('estados-proyecto.index')
                         ->with('success', 'Estado de proyecto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $estado = EstadoProyecto::findOrFail($id);
        
        if($estado->proyectos()->count() > 0) {
            return redirect()->route('estados-proyecto.index')
                             ->with('error', 'No se puede eliminar este estado porque tiene proyectos asociados.');
        }
        
        $estado->delete();

        return redirect()->route('estados-proyecto.index')
                         ->with('success', 'Estado de proyecto eliminado exitosamente.');
    }
}