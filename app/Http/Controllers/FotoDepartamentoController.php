<?php

namespace App\Http\Controllers;

use App\Models\FotoDepartamento;
use App\Models\Proyecto;
use App\Models\Edificio;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FotoDepartamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fotos = FotoDepartamento::with(['proyecto', 'edificio', 'departamento'])->get();
        return view('foto_departamentos.index', compact('fotos'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $proyectos = Proyecto::all();
        return view('foto_departamentos.create', compact('proyectos'));
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
            'edificio_id' => 'required|exists:edificios,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Almacenar la imagen
        $imagenPath = $request->file('imagen')->store('departamentos', 'public');

        FotoDepartamento::create([
            'proyecto_id' => $validated['proyecto_id'],
            'edificio_id' => $validated['edificio_id'],
            'departamento_id' => $validated['departamento_id'],
            'imagen' => $imagenPath,
        ]);

        return redirect()->route('foto-departamentos.index')
                         ->with('success', 'Foto de departamento creada exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $foto = FotoDepartamento::with(['proyecto', 'edificio', 'departamento'])->findOrFail($id);
        return view('foto_departamentos.show', compact('foto'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $foto = FotoDepartamento::findOrFail($id);
        $proyectos = Proyecto::all();
        $edificios = Edificio::where('proyecto_id', $foto->proyecto_id)->get();
        $departamentos = Departamento::where('edificio_id', $foto->edificio_id)->get();
        
        return view('foto_departamentos.edit', compact('foto', 'proyectos', 'edificios', 'departamentos'));
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
        $foto = FotoDepartamento::findOrFail($id);

        $validated = $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'edificio_id' => 'required|exists:edificios,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Actualizar la imagen si se proporciona una nueva
        if ($request->hasFile('imagen')) {
            // Eliminar la imagen anterior
            Storage::disk('public')->delete($foto->imagen);
            
            // Almacenar la nueva imagen
            $imagenPath = $request->file('imagen')->store('departamentos', 'public');
            $validated['imagen'] = $imagenPath;
        } else {
            unset($validated['imagen']);
        }

        $foto->update($validated);

        return redirect()->route('foto-departamentos.index')
                         ->with('success', 'Foto de departamento actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $foto = FotoDepartamento::findOrFail($id);
        
        // Eliminar la imagen del almacenamiento
        Storage::disk('public')->delete($foto->imagen);
        
        $foto->delete();

        return redirect()->route('foto-departamentos.index')
                         ->with('success', 'Foto de departamento eliminada exitosamente.');
    }

    /**
     * Obtener edificios por proyecto (AJAX)
     */
    public function getEdificios($proyectoId)
    {
        $edificios = Edificio::where('proyecto_id', $proyectoId)->get();
        return response()->json($edificios);
    }

    /**
     * Obtener departamentos por edificio (AJAX)
     */
    public function getDepartamentos($edificioId)
    {
        $departamentos = Departamento::where('edificio_id', $edificioId)->get();
        return response()->json($departamentos);
    }
}