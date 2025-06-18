<?php

namespace App\Filament\Resources\DepartamentoResource\Pages;

use App\Filament\Resources\DepartamentoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\CreateAction;

class ListDepartamentos extends ListRecords
{
    protected static string $resource = DepartamentoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Departamento'),
        ];
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            CreateAction::make() 
                ->label('Crear Departamento')
                ->url(route('filament.resources.departamentos.create'))
                ->icon('heroicon-o-plus')
                ->button(),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No hay departamentos registrados';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Haz clic en "Crear Departamento" para agregar uno nuevo';
    }
}