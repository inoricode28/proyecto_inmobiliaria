<?php

namespace App\Filament\Resources\ConsultaStockResource\Widgets;

use App\Models\EstadoDepartamento;
use App\Models\TipoFinanciamiento;
use App\Models\Departamento;
use App\Models\Edificio;
use Filament\Widgets\Widget;

class EstadosInmuebleWidget extends Widget
{
    protected static string $view = 'filament.resources.consulta-stock-resource.estados-immueble-widget';

    protected int|string|array $columnSpan = 'full';

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
                'count' => 0,
                'color' => $tipo->color,
                'descripcion' => $tipo->descripcion ?? '',
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
