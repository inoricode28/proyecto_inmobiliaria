<?php

namespace App\Filament\Resources\VendedorResource\Pages;

use App\Filament\Resources\VendedorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVendedor extends EditRecord
{
    protected static string $resource = VendedorResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
