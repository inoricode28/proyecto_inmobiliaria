<?php

namespace App\Http\Controllers;

use App\Models\TipoDepartamento;
use Illuminate\Http\Request;

class TipoDepartamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tipos = TipoDepartamento::withCount('departamentos')
                               ->orderBy('nombre')
                               ->get();
                               
        return view('tipos_departamento.index', compact('tipos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tipos_departamento.create');
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
            'nombre' => 'required|string|max:50|unique:tipos_departamento,nombre',
            'descripcion' => 'nullable|string|max:255'
        ]);

        TipoDepartamento::create($validated);

        return redirect()->route('tipos-departamento.index')
                         ->with('success', 'Tipo de departamento creado exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tipo = TipoDepartamento::with(['departamentos' => function($query) {
            $query->orderBy('numero');
        }])->findOrFail($id);
        
        return view('tipos_departamento.show', compact('tipo'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tipo = TipoDepartamento::findOrFail($id);
        return view('tipos_departamento.edit', compact('tipo'));
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
        $tipo = TipoDepartamento::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:tipos_departamento,nombre,'.$tipo->id,
            'descripcion' => 'nullable|string|max:255'
        ]);

        $tipo->update($validated);

        return redirect()->route('tipos-departamento.index')
                         ->with('success', 'Tipo de departamento actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tipo = TipoDepartamento::findOrFail($id);
        
        if($tipo->departamentos()->count() > 0) {
            return redirect()->route('tipos-departamento.index')
                             ->with('error', 'No se puede eliminar este tipo porque tiene departamentos asociados.');
        }
        
        $tipo->delete();

        return redirect()->route('tipos-departamento.index')
                         ->with('success', 'Tipo de departamento eliminado exitosamente.');
    }
}