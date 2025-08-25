<?php

namespace App\Filament\Resources\ProspectoResource\Pages;

use App\Filament\Resources\ProspectoResource;
use Filament\Resources\Pages\ViewRecord;

class ViewProspecto extends ViewRecord
{
    protected static string $resource = ProspectoResource::class;

    protected static string $view = 'filament.resources.panel-seguimiento-resource.view-prospecto-info';
}
