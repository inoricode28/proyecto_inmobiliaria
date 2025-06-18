<?php

namespace App\Filament\Resources\EmpresaResource\Pages;

use App\Filament\Resources\EmpresaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\CreateAction; 

class ListEmpresas extends ListRecords
{
    protected static string $resource = EmpresaResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nueva Empresa'),
        ];
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            CreateAction::make() 
                ->label('Crear Empresa')
                ->url(route('filament.resources.empresas.create'))
                ->icon('heroicon-o-plus')
                ->button(),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No hay empresas registradas';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Haz clic en "Crear Empresa" para agregar una nueva';
    }
}