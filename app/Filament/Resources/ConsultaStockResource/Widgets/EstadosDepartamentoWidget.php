<?php

namespace App\Filament\Resources\ConsultaStockResource\Widgets;

use App\Filament\Resources\ConsultaStockResource;
use App\Models\Departamento;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class EstadosDepartamentoWidget extends BaseWidget
{
    protected function getCards(): array
    {
        $estados = [
            ['nombre' => 'Bloqueado', 'descripcion' => 'Departamento bloqueado temporalmente'],
            ['nombre' => 'Disponible', 'descripcion' => 'Departamento disponible para venta'],
            ['nombre' => 'Separacion Temporal', 'descripcion' => 'Separado temporalmente por cliente'],
            ['nombre' => 'Separacion', 'descripcion' => 'Separado definitivamente por cliente'],
            ['nombre' => 'Pagado sin minuta', 'descripcion' => 'Pagado sin minuta firmada'],
            ['nombre' => 'Minuta', 'descripcion' => 'Minuta firmada'],
            ['nombre' => 'Cancelado', 'descripcion' => 'Venta cancelada'],
            ['nombre' => 'Listo Entrega', 'descripcion' => 'Listo para entrega al cliente'],
            ['nombre' => 'Entregado', 'descripcion' => 'Entregado al cliente'],
        ];

        $cards = [];

        foreach ($estados as $estado) {
            $count = Departamento::whereHas('estado', fn($q) =>
                $q->where('nombre', $estado['nombre'])
            )->count();

            $cards[] = Card::make($estado['nombre'], $count)
                ->description($estado['descripcion'])
                ->color($this->getColorForEstado($estado['nombre']))
                ->extraAttributes(['class' => 'cursor-pointer'])
                ->url(ConsultaStockResource::getUrl('index', [
                    'tableFilters' => [
                        'estado' => [
                            'value' => $estado['nombre']
                        ]
                    ]
                ]));
        }

        return $cards;
    }

    protected function getColorForEstado(string $estado): string
    {
        return match ($estado) {
            'Disponible' => 'success',
            'Separacion Temporal', 'Separacion' => 'warning',
            'Pagado sin minuta', 'Minuta' => 'info',
            'Bloqueado', 'Cancelado' => 'danger',
            'Listo Entrega', 'Entregado' => 'primary',
            default => 'gray',
        };
    }
}
