<?php

namespace App\Filament\Resources\FotoDepartamentoResource\Pages;

use App\Filament\Resources\FotoDepartamentoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFotoDepartamento extends ViewRecord
{
    protected static string $resource = FotoDepartamentoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}