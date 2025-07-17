<?php

namespace App\Filament\Resources\Proforma\ProformaResource\Pages;

use App\Filament\Resources\Proforma\ProformaResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditProforma extends EditRecord
{
    protected static string $resource = ProformaResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = Auth::id(); // o auth()->id()
        return $data;
    }
}
