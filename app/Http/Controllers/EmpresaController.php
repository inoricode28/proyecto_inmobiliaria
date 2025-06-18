<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $empresas = Empresa::withCount('proyectos')->get();
        return view('empresas.index', compact('empresas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('empresas.create');
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
            'ruc' => 'required|string|size:11|unique:empresas,ruc',
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'representante_legal' => 'required|string|max:100'
        ]);

        Empresa::create($validated);

        return redirect()->route('empresas.index')
                         ->with('success', 'Empresa creada exitosamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $empresa = Empresa::with('proyectos')->findOrFail($id);
        return view('empresas.show', compact('empresa'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $empresa = Empresa::findOrFail($id);
        return view('empresas.edit', compact('empresa'));
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
        $empresa = Empresa::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'ruc' => 'required|string|size:11|unique:empresas,ruc,'.$empresa->id,
            'direccion' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:100',
            'representante_legal' => 'required|string|max:100'
        ]);

        $empresa->update($validated);

        return redirect()->route('empresas.index')
                         ->with('success', 'Empresa actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $empresa = Empresa::findOrFail($id);
        
        // Verificar si tiene proyectos asociados
        if($empresa->proyectos()->count() > 0) {
            return redirect()->route('empresas.index')
                             ->with('error', 'No se puede eliminar la empresa porque tiene proyectos asociados.');
        }
        
        $empresa->delete();

        return redirect()->route('empresas.index')
                         ->with('success', 'Empresa eliminada exitosamente.');
    }
}