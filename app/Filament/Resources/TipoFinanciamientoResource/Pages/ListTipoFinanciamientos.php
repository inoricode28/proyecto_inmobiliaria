<?php

namespace App\Filament\Resources\TipoFinanciamientoResource\Pages;

use App\Filament\Resources\TipoFinanciamientoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;

class ListTipoFinanciamientos extends ListRecords
{
    protected static string $resource = TipoFinanciamientoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Tipo de Financiamiento'),
        ];
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Tables\Actions\Action::make('create')
                ->label('Crear Tipo de Financiamiento')
                ->url(route('filament.resources.tipo-financiamientos.create'))
                ->icon('heroicon-o-plus')
                ->button(),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No hay tipos de financiamiento registrados';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Haz clic en "Crear Tipo de Financiamiento" para agregar uno nuevo';
    }
}
