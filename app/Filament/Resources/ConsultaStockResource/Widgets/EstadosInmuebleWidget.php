<?php

namespace App\Filament\Resources\ConsultaStockResource\Widgets;

use App\Models\EstadoDepartamento;
use App\Models\TipoFinanciamiento;
use App\Models\Departamento;
use App\Models\Edificio;
use App\Models\TipoInmueble;
use Filament\Widgets\Widget;

class EstadosInmuebleWidget extends Widget
{
    protected static string $view = 'filament.resources.consulta-stock-resource.estados-immueble-widget';

    protected int|string|array $columnSpan = 'full';

    // Propiedad pública para el filtro con Livewire
    public $selectedTipoInmueble = '';

    protected function getViewData(): array
    {
        $edificioId = request()->get('edificio_id') ?: Edificio::first()?->id;
        $estados = EstadoDepartamento::orderBy('nombre')->get();
        $tiposInmueble = TipoInmueble::all(); // Para el combobox

        // Consulta base con filtro aplicado
        $query = Departamento::query()
            ->where('edificio_id', $edificioId)
            ->when($this->selectedTipoInmueble, function ($query) {
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
                'tipo_id' => $type->id // Agregamos el ID para referencia
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

            // Solo mostrar filas con datos o cuando no hay filtro
            if ($row['totals'] > 0 || !$this->selectedTipoInmueble) {
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
            'selectedTipo' => $this->selectedTipoInmueble
        ];
    }

    // Métodos adicionales (se mantienen igual)
    public function getEstadosData(): array
    {
        return EstadoDepartamento::withCount('departamentos')->get()->map(function ($estado) {
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
        return TipoFinanciamiento::all()->map(function ($tipo) {
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

        return Departamento::with(['estadoDepartamento', 'fotoDepartamentos'])
            ->where('edificio_id', $edificioId)
            ->orderBy('num_piso', 'desc')
            ->orderBy('num_departamento')
            ->get()
            ->groupBy('num_piso');
    }
}
