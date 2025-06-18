<?php

namespace App\Filament\Resources\EstadoProyectoResource\Pages;

use App\Filament\Resources\EstadoProyectoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use App\Models\EstadoProyecto;

class ListEstadoProyectos extends ListRecords
{
    protected static string $resource = EstadoProyectoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Estado'),
        ];
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Tables\Actions\Action::make('create')
                ->label('Crear Estado')
                ->url(route('filament.resources.estado-proyectos.create'))
                ->icon('heroicon-o-plus')
                ->button(),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No hay estados de proyecto registrados';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Haz clic en "Crear Estado" para agregar uno nuevo';
    }
}