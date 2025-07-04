<?php

namespace App\Filament\Resources\ConsultaStockResource\Widgets;

use App\Models\Departamento;
use App\Models\Edificio;
use Filament\Widgets\Widget;

class PisosInmuebleWidget extends Widget
{
    protected static string $view = 'filament.resources.consulta-stock-resource.pisos-inmueble';

    protected int|string|array $columnSpan = 'full';

   public function getPisosData()
{
    $edificioId = request()->get('edificio_id') ?: Edificio::first()?->id;

    return Departamento::with(['estadoDepartamento', 'fotoDepartamentos'])
    ->where('edificio_id', $edificioId)
    ->orderBy('num_piso', 'asc')
    ->orderBy('num_departamento')
    ->get()
    ->groupBy('num_piso');

}

}
