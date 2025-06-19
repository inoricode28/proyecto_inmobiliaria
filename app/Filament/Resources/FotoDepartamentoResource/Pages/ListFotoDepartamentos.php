<?php

namespace App\Filament\Resources\FotoDepartamentoResource\Pages;

use App\Filament\Resources\FotoDepartamentoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\CreateAction;

class ListFotoDepartamentos extends ListRecords  
{
    protected static string $resource = FotoDepartamentoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nueva Foto')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            CreateAction::make()
                ->label('Agregar Foto')
                ->url(route('filament.resources.foto-departamentos.create'))
                ->icon('heroicon-o-plus')
                ->button(),
        ];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No hay fotos registradas';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Haz clic en "Agregar Foto" para subir imágenes de departamentos';
    }
}