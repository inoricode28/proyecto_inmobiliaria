<?php

namespace App\Http\Controllers;

use App\Models\EstadoDepartamento;
use Illuminate\Http\Request;

class EstadoDepartamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $estados = EstadoDepartamento::withCount('departamentos')->get();
        return view('estados_departamento.index', compact('estados'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('estados_departamento.create');
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
            'nombre' => 'required|string|max:50|unique:estados_departamento,nombre',
            'descripcion' => 'nullable|string|max:255'
        ]);

        EstadoDepartamento::create($validated);

        return redirect()->route('estados-departamento.index')
                         ->with('success', 'Estado de departamento creado exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $estado = EstadoDepartamento::with('departamentos')->findOrFail($id);
        return view('estados_departamento.show', compact('estado'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $estado = EstadoDepartamento::findOrFail($id);
        return view('estados_departamento.edit', compact('estado'));
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
        $estado = EstadoDepartamento::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:estados_departamento,nombre,'.$estado->id,
            'descripcion' => 'nullable|string|max:255'
        ]);

        $estado->update($validated);

        return redirect()->route('estados-departamento.index')
                         ->with('success', 'Estado de departamento actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $estado = EstadoDepartamento::findOrFail($id);
        
        // Verificar si tiene departamentos asociados
        if($estado->departamentos()->count() > 0) {
            return redirect()->route('estados-departamento.index')
                             ->with('error', 'No se puede eliminar este estado porque tiene departamentos asociados.');
        }
        
        $estado->delete();

        return redirect()->route('estados-departamento.index')
                         ->with('success', 'Estado de departamento eliminado exitosamente.');
    }
}