<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Edificio;
use App\Models\TipoDepartamento;
use App\Models\EstadoDepartamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departamentos = Departamento::with(['edificio', 'tipo', 'estado'])->get();
        return view('departamentos.index', compact('departamentos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $edificios = Edificio::all();
        $tipos = TipoDepartamento::all();
        $estados = EstadoDepartamento::all();
        
        return view('departamentos.create', compact('edificios', 'tipos', 'estados'));
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
            'edificio_id' => 'required|exists:edificios,id',
            'numero' => 'required|string|max:20',
            'piso' => 'required|integer',
            'area_total' => 'required|numeric',
            'area_construida' => 'required|numeric',
            'numero_habitaciones' => 'required|integer',
            'numero_banos' => 'required|integer',
            'tiene_balcon' => 'required|boolean',
            'tipo_departamento_id' => 'required|exists:tipos_departamento,id',
            'estado_departamento_id' => 'required|exists:estados_departamento,id',
            'precio' => 'required|numeric'
        ]);

        Departamento::create($validated);

        return redirect()->route('departamentos.index')
                         ->with('success', 'Departamento creado exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $departamento = Departamento::with(['edificio', 'tipo', 'estado'])->findOrFail($id);
        return view('departamentos.show', compact('departamento'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $departamento = Departamento::findOrFail($id);
        $edificios = Edificio::all();
        $tipos = TipoDepartamento::all();
        $estados = EstadoDepartamento::all();
        
        return view('departamentos.edit', compact('departamento', 'edificios', 'tipos', 'estados'));
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
        $departamento = Departamento::findOrFail($id);

        $validated = $request->validate([
            'edificio_id' => 'required|exists:edificios,id',
            'numero' => 'required|string|max:20',
            'piso' => 'required|integer',
            'area_total' => 'required|numeric',
            'area_construida' => 'required|numeric',
            'numero_habitaciones' => 'required|integer',
            'numero_banos' => 'required|integer',
            'tiene_balcon' => 'required|boolean',
            'tipo_departamento_id' => 'required|exists:tipos_departamento,id',
            'estado_departamento_id' => 'required|exists:estados_departamento,id',
            'precio' => 'required|numeric'
        ]);

        $departamento->update($validated);

        return redirect()->route('departamentos.index')
                         ->with('success', 'Departamento actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $departamento = Departamento::findOrFail($id);
        $departamento->delete();

        return redirect()->route('departamentos.index')
                         ->with('success', 'Departamento eliminado exitosamente.');
    }
}