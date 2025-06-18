<?php

namespace App\Filament\Resources\EstadoDepartamentoResource\Pages;

use App\Filament\Resources\EstadoDepartamentoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;

class ListEstadoDepartamentos extends ListRecords
{
    protected static string $resource = EstadoDepartamentoResource::class;

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
                ->url(route('filament.resources.estado-departamentos.create'))
                ->icon('heroicon-o-plus')
                ->button(),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No hay estados de departamento registrados';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Haz clic en "Crear Estado" para agregar uno nuevo';
    }
}