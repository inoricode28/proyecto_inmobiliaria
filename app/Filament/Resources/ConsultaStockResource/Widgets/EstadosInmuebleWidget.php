<?php

namespace App\Filament\Resources\ConsultaStockResource\Widgets;

use App\Models\EstadoDepartamento;
use App\Models\TipoFinanciamiento;
use App\Models\Departamento;
use App\Models\Edificio;
use App\Models\TipoInmueble;
use App\Models\Proyecto;
use Filament\Widgets\Widget;
use Livewire\WithFileUploads;

class EstadosInmuebleWidget extends Widget
{
    use WithFileUploads;

    protected static string $view = 'filament.resources.consulta-stock-resource.estados-immueble-widget';

    protected int|string|array $columnSpan = 'full';

    // Propiedades públicas
    public $selectedTipoInmueble = '';
    public $selectedProyecto = '';
    public $fotosModal = [];
    public $departamentoSeleccionado = null;

    protected function getViewData(): array
    {
        $edificioId = request()->get('edificio_id') ?: Edificio::first()?->id;
        $estados = EstadoDepartamento::orderBy('nombre')->get();
        $tiposInmueble = TipoInmueble::all();
        $proyectos = Proyecto::orderBy('nombre')->get();

        // Consulta base con filtros
        $query = Departamento::query()
            ->when($edificioId, function($query) use ($edificioId) {
                $query->where('edificio_id', $edificioId);
            })
            ->when($this->selectedProyecto, function($query) {
                $query->where('proyecto_id', $this->selectedProyecto);
            })
            ->when($this->selectedTipoInmueble, function($query) {
                $query->where('tipo_inmueble_id', $this->selectedTipoInmueble);
            });

        // Agrupar datos por tipo y estado
        $data = $query
            ->selectRaw('tipo_inmueble_id, estado_departamento_id, COUNT(*) as count')
            ->groupBy('tipo_inmueble_id', 'estado_departamento_id')
            ->get()
            ->groupBy('tipo_inmueble_id');

        // Preparar datos para la tabla
        $propertyTypes = $this->selectedTipoInmueble
            ? TipoInmueble::where('id', $this->selectedTipoInmueble)->get()->keyBy('id')
            : TipoInmueble::all()->keyBy('id');

        $rowData = [];
        $columnTotals = array_fill_keys($estados->pluck('id')->toArray(), 0);
        $grandTotal = 0;

        foreach ($propertyTypes as $type) {
            $row = [
                'name' => $type->nombre,
                'totals' => 0,
                'tipo_id' => $type->id
            ];

            foreach ($estados as $estado) {
                $count = $data->get($type->id, collect())
                    ->where('estado_departamento_id', $estado->id)
                    ->first()->count ?? 0;

                $row[$estado->id] = $count;
                $row['totals'] += $count;
                $columnTotals[$estado->id] += $count;
                $grandTotal += $count;
            }

            if ($row['totals'] > 0 || (!$this->selectedTipoInmueble && !$this->selectedProyecto)) {
                $rowData[] = $row;
            }
        }

        // Fila de totales (solo si hay datos)
        if (!empty($rowData)) {
            $totalRow = [
                'name' => 'Total',
                'totals' => $grandTotal,
                'tipo_id' => 'total'
            ];
            foreach ($estados as $estado) {
                $totalRow[$estado->id] = $columnTotals[$estado->id];
            }
            $rowData[] = $totalRow;
        }

        return [
            'rows' => $rowData,
            'estados' => $estados,
            'edificio' => Edificio::find($edificioId),
            'tiposInmueble' => $tiposInmueble,
            'proyectos' => $proyectos,
            'selectedTipo' => $this->selectedTipoInmueble
        ];
    }

    public function getEstadosData(): array
    {
        return EstadoDepartamento::withCount(['departamentos' => function($query) {
            $query->when($this->selectedProyecto, function($q) {
                $q->where('proyecto_id', $this->selectedProyecto);
            });
        }])->get()->map(function ($estado) {
            return [
                'nombre' => $estado->nombre,
                'count' => $estado->departamentos_count,
                'color' => $estado->color,
                'descripcion' => $estado->descripcion ?? ''
            ];
        })->values()->all();
    }

    public function getFinanciamientosData(): array
    {
        return TipoFinanciamiento::withCount(['departamentos' => function($query) {
            $query->when($this->selectedProyecto, function($q) {
                $q->where('proyecto_id', $this->selectedProyecto);
            });
        }])->get()->map(function ($tipo) {
            return [
                'nombre' => $tipo->nombre,
                'count' => $tipo->departamentos_count,
                'color' => $tipo->color,
                'descripcion' => $tipo->descripcion ?? ''
            ];
        })->values()->all();
    }

    public function getPisosData()
    {
        $edificioId = request()->get('edificio_id') ?: Edificio::first()?->id;
        
        return Departamento::with([
                'estadoDepartamento', 
                'fotoDepartamentos',
                'proyecto',
                'edificio'
            ])
            ->when($this->selectedProyecto, function($query) {
                $query->where('proyecto_id', $this->selectedProyecto);
            })
            ->when(!$this->selectedProyecto && $edificioId, function($query) use ($edificioId) {
                $query->where('edificio_id', $edificioId);
            })
            ->orderBy('num_piso', 'desc')
            ->orderBy('num_departamento')
            ->get()
            ->groupBy('num_piso');
    }

    public function mostrarFotos($departamentoId)
    {
        $departamento = Departamento::with('fotoDepartamentos')->find($departamentoId);
        
        if ($departamento && $departamento->fotoDepartamentos->isNotEmpty()) {
            $this->fotosModal = $departamento->fotoDepartamentos
                ->map(fn($foto) => asset('storage/'.$foto->imagen))
                ->toArray();
            $this->departamentoSeleccionado = $departamento->num_departamento;
            $this->dispatchBrowserEvent('abrirModalFotos');
        }
    }

    public function cerrarModal()
    {
        $this->fotosModal = [];
        $this->departamentoSeleccionado = null;
    }
}