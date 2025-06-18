<?php

namespace App\Filament\Resources\TipoDepartamentoResource\Pages;

use App\Filament\Resources\TipoDepartamentoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\CreateAction; 


class ListTipoDepartamentos extends ListRecords
{
    protected static string $resource = TipoDepartamentoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Tipo'),
        ];
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            CreateAction::make() 
                ->label('Crear Tipo')
                ->url(route('filament.resources.tipo-departamentos.create'))
                ->icon('heroicon-o-plus')
                ->button(),
        ];
    }

    

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No hay tipos de departamento registrados';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Haz clic en "Crear Tipo" para agregar uno nuevo';
    }
}