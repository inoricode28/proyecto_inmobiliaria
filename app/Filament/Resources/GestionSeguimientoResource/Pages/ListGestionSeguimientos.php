<?php

namespace App\Filament\Resources\GestionSeguimientoResource\Pages;

use App\Filament\Resources\GestionSeguimientoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGestionSeguimientos extends ListRecords
{
    protected static string $resource = GestionSeguimientoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Prospecto')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\GestionSeguimientoResource\Widgets\ProspectosStats::class,
            ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No hay prospectos registrados';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Haz clic en "Nuevo Prospecto" para agregar uno nuevo';
    }

    protected function getTableEmptyStateIcon(): string
    {
        return 'heroicon-o-user-group';
    }
}