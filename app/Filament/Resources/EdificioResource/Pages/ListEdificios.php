<?php

namespace App\Filament\Resources\EdificioResource\Pages;

use App\Filament\Resources\EdificioResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\CreateAction; 

class ListEdificios extends ListRecords
{
    protected static string $resource = EdificioResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Edificio'),
        ];
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            CreateAction::make() 
                ->label('Crear Edificio')
                ->url(route('filament.resources.edificios.create'))
                ->icon('heroicon-o-plus')
                ->button(),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No hay edificios registrados';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Haz clic en "Crear Edificio" para agregar uno nuevo';
    }
}