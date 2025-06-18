<?php

namespace App\Filament\Resources\ProyectoResource\Pages;

use App\Filament\Resources\ProyectoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\CreateAction; 

class ListProyectos extends ListRecords
{
    protected static string $resource = ProyectoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Proyecto'),
        ];
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            CreateAction::make() 
                ->label('Crear Proyecto')
                ->url(route('filament.resources.proyectos.create'))
                ->icon('heroicon-o-plus')
                ->button(),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No hay proyectos registrados';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Haz clic en "Crear Proyecto" para agregar uno nuevo';
    }
}