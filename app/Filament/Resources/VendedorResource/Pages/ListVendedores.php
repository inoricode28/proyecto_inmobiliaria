<?php

namespace App\Filament\Resources\VendedorResource\Pages;

use App\Filament\Resources\VendedorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\CreateAction;

class ListVendedores extends ListRecords
{
    protected static string $resource = VendedorResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Vendedor'),
        ];
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            CreateAction::make()
                ->label('Crear Vendedor')
                ->url(static::getResource()::getUrl('create')) // Updated this line
                ->icon('heroicon-o-plus')
                ->button(),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No hay vendedores registrados';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Haz clic en "Crear Vendedor" para agregar uno nuevo';
    }
}
