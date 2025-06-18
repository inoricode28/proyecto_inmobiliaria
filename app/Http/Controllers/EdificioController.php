<?php

namespace App\Http\Controllers;

use App\Models\Edificio;
use App\Models\Proyecto;
use Illuminate\Http\Request;

class EdificioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $edificios = Edificio::with(['proyecto', 'departamentos'])->get();
        return view('edificios.index', compact('edificios'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $proyectos = Proyecto::all();
        return view('edificios.create', compact('proyectos'));
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
            'proyecto_id' => 'required|exists:proyectos,id',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'cantidad_pisos' => 'required|integer|min:1',
            'cantidad_departamentos' => 'required|integer|min:1',
            'fecha_inicio' => 'required|date',
            'fecha_entrega' => 'required|date|after_or_equal:fecha_inicio'
        ]);

        Edificio::create($validated);

        return redirect()->route('edificios.index')
                         ->with('success', 'Edificio creado exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $edificio = Edificio::with(['proyecto', 'departamentos'])->findOrFail($id);
        return view('edificios.show', compact('edificio'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edificio = Edificio::findOrFail($id);
        $proyectos = Proyecto::all();
        
        return view('edificios.edit', compact('edificio', 'proyectos'));
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
        $edificio = Edificio::findOrFail($id);

        $validated = $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'cantidad_pisos' => 'required|integer|min:1',
            'cantidad_departamentos' => 'required|integer|min:1',
            'fecha_inicio' => 'required|date',
            'fecha_entrega' => 'required|date|after_or_equal:fecha_inicio'
        ]);

        $edificio->update($validated);

        return redirect()->route('edificios.index')
                         ->with('success', 'Edificio actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $edificio = Edificio::findOrFail($id);
        
        // Verificar si tiene departamentos asociados
        if($edificio->departamentos()->count() > 0) {
            return redirect()->route('edificios.index')
                             ->with('error', 'No se puede eliminar el edificio porque tiene departamentos asociados.');
        }
        
        $edificio->delete();

        return redirect()->route('edificios.index')
                         ->with('success', 'Edificio eliminado exitosamente.');
    }
}