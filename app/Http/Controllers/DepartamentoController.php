<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Edificio;
use App\Models\TipoDepartamento;
use App\Models\TipoInmueble;
use App\Models\Estado;
use App\Models\EstadoDepartamento;
use App\Models\Proyecto;
use App\Models\Vista;
use App\Models\Moneda;
use App\Models\TipoFinanciamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartamentoController extends Controller
{
    public function index()
    {
        try {
            $departamentos = Departamento::with([
                'proyecto',
                'edificio',
                'tipoInmueble',
                'tipoDepartamento',
                'estadoDepartamento',
                'vista',
                'moneda',
                'estado'
            ])
            ->orderBy('edificio_id')
            ->orderBy('num_piso')
            ->orderBy('numero_inicial')
            ->get();

            return view('departamentos.index', compact('departamentos'));
        } catch (\Exception $e) {
            Log::error('Error al listar departamentos: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar la lista de departamentos');
        }
    }

    public function create()
    {
        try {
            return view('departamentos.create', [
                'proyectos' => Proyecto::activos()->get(),
                'edificios' => Edificio::with('proyecto')->activos()->get(),
                'tiposInmueble' => TipoInmueble::orderBy('nombre')->get(),
                'tiposDepartamento' => TipoDepartamento::orderBy('nombre')->get(),
                'estadosDepartamento' => EstadoDepartamento::orderBy('nombre')->get(),
                'tipoFinanciamiento' => TipoFinanciamiento::orderBy('nombre')->get(),
                'vistas' => Vista::orderBy('nombre')->get(),
                'monedas' => Moneda::activas()->get(),
                'estados' => Estado::whereIn('id', [1, 2])->get()
            ]);
        } catch (\Exception $e) {
            Log::error('Error al mostrar formulario de creación: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar el formulario de creación');
        }
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        try {
            DB::beginTransaction();

            if ($validated['bono_techo_propio'] && empty($validated['num_bono_tp'])) {
                $validated['num_bono_tp'] = 'BTP-' . strtoupper(uniqid());
            }

            $departamento = Departamento::create($validated);

            DB::commit();

            return redirect()->route('departamentos.show', $departamento)
                             ->with('success', 'Departamento creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear departamento: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al crear el departamento: ' . $e->getMessage());
        }
    }

    public function show(Departamento $departamento)
    {
        try {
            $departamento->load([
                'proyecto',
                'edificio',
                'tipoInmueble',
                'tipoDepartamento',
                'estadoDepartamento',
                'vista',
                'moneda',
                'estado',
                'tipoFinanciamiento',
            ]);

            return view('departamentos.show', compact('departamento'));
        } catch (\Exception $e) {
            Log::error('Error al mostrar departamento: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar el departamento');
        }
    }

    public function edit(Departamento $departamento)
    {
        try {
            return view('departamentos.edit', [
                'departamento' => $departamento,
                'proyectos' => Proyecto::activos()->get(),
                'edificios' => Edificio::with('proyecto')->activos()->get(),
                'tiposInmueble' => TipoInmueble::orderBy('nombre')->get(),
                'tiposDepartamento' => TipoDepartamento::orderBy('nombre')->get(),
                'estadosDepartamento' => EstadoDepartamento::orderBy('nombre')->get(),
                'tipoFinanciamiento' => TipoFinanciamiento::orderBy('nombre')->get(),
                'vistas' => Vista::orderBy('nombre')->get(),
                'monedas' => Moneda::activas()->get(),
                'estados' => Estado::whereIn('id', [1, 2])->get()
            ]);
        } catch (\Exception $e) {
            Log::error('Error al mostrar formulario de edición: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar el formulario de edición');
        }
    }

    public function update(Request $request, Departamento $departamento)
    {
        $validated = $this->validateRequest($request, $departamento);

        try {
            DB::beginTransaction();

            if ($validated['bono_techo_propio'] && empty($validated['num_bono_tp'])) {
                $validated['num_bono_tp'] = 'BTP-' . strtoupper(uniqid());
            }

            $departamento->update($validated);

            DB::commit();

            return redirect()->route('departamentos.show', $departamento)
                             ->with('success', 'Departamento actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar departamento: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al actualizar el departamento: ' . $e->getMessage());
        }
    }

    public function destroy(Departamento $departamento)
    {
        try {
            if (method_exists($departamento, 'operaciones') && $departamento->operaciones()->exists()) {
                return back()->with('error', 'No se puede eliminar el departamento porque tiene operaciones relacionadas.');
            }

            $departamento->delete();

            return redirect()->route('departamentos.index')
                             ->with('success', 'Departamento eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar departamento: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar el departamento');
        }
    }

    protected function validateRequest(Request $request, ?Departamento $departamento = null): array
    {
        $rules = [
            'proyecto_id' => 'nullable|exists:proyectos,id',
            'edificio_id' => 'required|exists:edificios,id',
            'tipo_inmueble_id' => 'nullable|exists:tipo_inmuebles,id',
            'tipo_departamento_id' => 'required|exists:tipos_departamento,id',
            'estado_departamento_id' => 'required|exists:estados_departamento,id',
            'vista_id' => 'nullable|exists:vistas,id',
            'moneda_id' => 'required|exists:monedas,id',
            'numero_inicial' => 'required|string|max:20|unique:departamentos,numero_inicial' . ($departamento ? ",{$departamento->id}" : ''),
            'numero_final' => 'nullable|string|max:20',
            'ficha_indep' => 'nullable|string|max:50',
            'num_departamento' => 'nullable|string|max:255',
            'num_piso' => 'required|integer|min:0|max:150',
            'num_dormitorios' => 'required|integer|min:0|max:10',
            'num_bano' => 'required|integer|min:0|max:6',
            'num_certificado' => 'nullable|string|max:50',
            'bono_techo_propio' => 'boolean',
            'num_bono_tp' => 'nullable|string|max:50',
            'cantidad_uit' => 'nullable|numeric|min:0',
            'codigo_bancario' => 'nullable|string|max:50',
            'codigo_catastral' => 'nullable|string|max:50',
            'precio' => 'required|numeric|min:0|max:9999999.99',
            'Precio_lista' => 'nullable|numeric|min:0|max:9999999.99',
            'Precio_venta' => 'nullable|numeric|min:0|max:9999999.99',
            'descuento' => 'nullable|numeric|min:0|max:100',
            'predio_m2' => 'nullable|numeric|min:0|max:9999.99',
            'terreno' => 'nullable|numeric|min:0|max:9999.99',
            'techada' => 'nullable|numeric|min:0|max:9999.99',
            'construida' => 'nullable|numeric|min:0|max:9999.99',
            'terraza' => 'nullable|numeric|min:0|max:9999.99',
            'jardin' => 'nullable|numeric|min:0|max:9999.99',
            'adicional' => 'nullable|string|max:100',
            'vendible' => 'boolean',
            'frente' => 'nullable|numeric|min:0|max:999.99',
            'derecha' => 'nullable|numeric|min:0|max:999.99',
            'izquierda' => 'nullable|numeric|min:0|max:999.99',
            'fondo' => 'nullable|numeric|min:0|max:999.99',
            'direccion' => 'required|string|max:500',
            'observaciones' => 'nullable|string|max:1000',
            'estado_id' => 'required|exists:estados,id',
            'tipo_financiamiento_id' => 'required|exists:tipo_financiamiento,id',
        ];

        return $request->validate($rules);
    }
}
