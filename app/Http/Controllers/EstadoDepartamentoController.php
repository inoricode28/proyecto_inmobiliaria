<?php

namespace App\Http\Controllers;

use App\Models\EstadoDepartamento;
use Illuminate\Http\Request;

class EstadoDepartamentoController extends Controller
{
    /**
     * Muestra una lista de los estados de departamento.
     */
    public function index()
    {
        $estados = EstadoDepartamento::withCount('departamentos')->get();
        return view('estados_departamento.index', compact('estados'));
    }

    /**
     * Muestra el formulario para crear un nuevo estado.
     */
    public function create()
    {
        return view('estados_departamento.create');
    }

    /**
     * Almacena un nuevo estado en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:estados_departamento,nombre',
            'descripcion' => 'nullable|string|max:255',
        ]);

        EstadoDepartamento::create($validated);

        return redirect()->route('estados-departamento.index')
                         ->with('success', 'Estado de departamento creado exitosamente.');
    }

    /**
     * Muestra un estado de departamento específico.
     */
    public function show($id)
    {
        $estado = EstadoDepartamento::with('departamentos')->findOrFail($id);
        return view('estados_departamento.show', compact('estado'));
    }

    /**
     * Muestra el formulario para editar un estado existente.
     */
    public function edit($id)
    {
        $estado = EstadoDepartamento::findOrFail($id);
        return view('estados_departamento.edit', compact('estado'));
    }

    /**
     * Actualiza un estado de departamento en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $estado = EstadoDepartamento::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:estados_departamento,nombre,' . $estado->id,
            'descripcion' => 'nullable|string|max:255',
        ]);

        $estado->update($validated);

        return redirect()->route('estados-departamento.index')
                         ->with('success', 'Estado de departamento actualizado exitosamente.');
    }

    /**
     * Elimina un estado de departamento si no tiene departamentos relacionados.
     */
    public function destroy($id)
    {
        $estado = EstadoDepartamento::findOrFail($id);

        if ($estado->departamentos()->exists()) {
            return redirect()->route('estados-departamento.index')
                             ->with('error', 'No se puede eliminar este estado porque tiene departamentos asociados.');
        }

        $estado->delete();

        return redirect()->route('estados-departamento.index')
                         ->with('success', 'Estado de departamento eliminado exitosamente.');
    }
}
