<?php

namespace App\Filament\Resources\ConsultaStockResource\Widgets;

use App\Models\Departamento;
use App\Models\EstadoDepartamento;
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
}
