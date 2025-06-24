<?php

namespace App\Filament\Resources\ConsultaStockResource\Widgets;

use App\Filament\Resources\ConsultaStockResource;
use App\Models\Departamento;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class EstadosInmuebleWidget extends BaseWidget
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
            $count = Departamento::whereHas('estadoDepartamento', function ($q) use ($estado) {
                $q->where('nombre', $estado['nombre']);
            })->count();

            $cards[] = Card::make($estado['nombre'], $count)
                ->description($estado['descripcion'])
                ->color($this->getColorForEstado($estado['nombre']))
                ->extraAttributes([
                    'class' => $this->getCardColorClass($estado['nombre']) .
                               ' text-white shadow-md text-center font-semibold px-4 py-3',
                ])
                ->url(ConsultaStockResource::getUrl('index', [
                    'tableFilters' => [
                        'estado' => [
                            'value' => $estado['nombre'],
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

    protected function getCardColorClass(string $estado): string
    {
        return match ($estado) {
            'Disponible' => 'bg-green-500',
            'Separacion Temporal', 'Separacion' => 'bg-yellow-500',
            'Pagado sin minuta', 'Minuta' => 'bg-blue-500',
            'Bloqueado', 'Cancelado' => 'bg-red-500',
            'Listo Entrega', 'Entregado' => 'bg-indigo-600',
            default => 'bg-gray-400',
        };
    }
}
