<?php

namespace App\Filament\Resources\ConsultaStockResource\Widgets;

use App\Models\TipoFinanciamiento;
use App\Models\TipoInmueble;
use App\Models\Departamento;
use App\Models\Edificio;
use Filament\Widgets\Widget;


class FinanciamientosInmuebleWidget extends Widget
{
    protected static string $view = 'filament.resources.consulta-stock-resource.financiamientos-inmueble';

protected function getViewData(): array
{
    return $this->getFinanciamientosData();
}
    protected int|string|array $columnSpan = 'full';

    public function getFinanciamientosData(): array
    {
        $edificioId = request()->get('edificio_id') ?: Edificio::first()?->id;
        $edificio = $edificioId ? Edificio::find($edificioId) : null;
        $financingTypes = TipoFinanciamiento::orderBy('nombre')->get();
        $propertyTypes = TipoInmueble::all()->keyBy('id');

        // Agrupar datos por tipo de inmueble y financiamiento
        $data = Departamento::query()
            ->where('edificio_id', $edificioId)
            ->selectRaw('tipo_inmueble_id, tipos_financiamiento_id, COUNT(*) as count')
            ->groupBy('tipo_inmueble_id', 'tipos_financiamiento_id')
            ->get()
            ->groupBy('tipo_inmueble_id');

        // Calcular totales
        $rowData = [];
        $columnTotals = array_fill_keys($financingTypes->pluck('id')->toArray(), 0);
        $grandTotal = 0;

        foreach ($propertyTypes as $type) {
            $row = [
                'name' => $type->nombre,
                'totals' => 0,
            ];

            foreach ($financingTypes as $financing) {
                $count = $data->get($type->id, collect())
                    ->where('tipos_financiamiento_id', $financing->id)
                    ->first()->count ?? 0;

                $row[$financing->id] = $count;
                $row['totals'] += $count;
                $columnTotals[$financing->id] += $count;
                $grandTotal += $count;
            }

            $rowData[] = $row;
        }

        // Agregar fila de totales
        $totalRow = [
            'name' => 'Total',
            'totals' => $grandTotal,
        ];
        foreach ($financingTypes as $financing) {
            $totalRow[$financing->id] = $columnTotals[$financing->id];
        }
        $rowData[] = $totalRow;

        return [
            'rows' => $rowData,
            'financingTypes' => $financingTypes,
            'edificio' => $edificio,
        ];
    }
}
