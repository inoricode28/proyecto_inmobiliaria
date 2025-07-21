<?php

namespace App\Filament\Resources\PanelSeguimientoResource\Pages;

use App\Filament\Resources\PanelSeguimientoResource;
use Filament\Resources\Pages\Page;
use App\Models\Prospecto;
use App\Models\Tarea;

class ViewProspectoInfo extends Page
{
    protected static string $resource = PanelSeguimientoResource::class;
    
    protected static string $view = 'filament.resources.panel-seguimiento-resource.view-prospecto-info';

    public Prospecto $prospecto;
    public $ultimaTareaPendiente;

    public function mount($record)
    {
        $this->prospecto = Prospecto::with([
            'tareas' => fn ($q) => $q->latest()
        ])->findOrFail($record);

        $this->ultimaTareaPendiente = Tarea::where('prospecto_id', $record)
            ->whereDate('fecha_realizar', '>=', now())
            ->orderBy('fecha_realizar')
            ->first();
    }
}

