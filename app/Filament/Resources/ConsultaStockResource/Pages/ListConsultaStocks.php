<?php

namespace App\Filament\Resources\ConsultaStockResource\Pages;

use App\Filament\Resources\ConsultaStockResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsultaStocks extends ListRecords
{
    protected static string $resource = ConsultaStockResource::class;

    protected function getActions(): array
    {
        return [
            // Si necesitas acciones adicionales en la página de lista
            // Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            //ConsultaStockResource\Widgets\EstadosDepartamentoWidget::class,
            ConsultaStockResource\Widgets\EstadosInmuebleWidget::class,
        ];
    }

    protected function getTitle(): string
    {
        return 'Listado de Departamentos por Estado';
    }
}