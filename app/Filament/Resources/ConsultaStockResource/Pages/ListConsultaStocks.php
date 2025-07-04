<?php

namespace App\Filament\Resources\ConsultaStockResource\Pages;

use App\Filament\Resources\ConsultaStockResource;
use Filament\Resources\Pages\ListRecords;

class ListConsultaStocks extends ListRecords
{
    protected static string $resource = ConsultaStockResource::class;





    protected function getTableRecordsPerPage(): int
{
    return PHP_INT_MAX; // Número muy grande para mostrar todos los registros
}

protected function getTableRecordsPerPageSelectOptions(): array
{
    return [PHP_INT_MAX => 'Todos']; // Opción única
}

protected function getDefaultTableRecordsPerPageSelectOption(): int
{
    return PHP_INT_MAX;
}

    protected function isTablePaginationEnabled(): bool
    {
        return false; // Desactiva completamente la paginación
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ConsultaStockResource\Widgets\EstadosInmuebleWidget::class,
            ConsultaStockResource\Widgets\FinanciamientosInmuebleWidget::class,
        ];
    }

    protected function getTitle(): string
    {
        return 'Lista de Stock por Piso';
    }}
