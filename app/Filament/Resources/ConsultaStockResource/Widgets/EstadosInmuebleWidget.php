<?php

namespace App\Filament\Resources\ConsultaStockResource\Widgets;

use App\Models\Departamento;
use Filament\Widgets\Widget;

class EstadosInmuebleWidget extends Widget
{
    protected static string $view = 'filament.resources.consulta-stock-resource.estados-immueble-widget';
    
    protected int|string|array $columnSpan = 'full';

    public function getEstadosData(): array
    {
        $estados = [
            'Bloquedo' => ['color' => 'bg-red-500', 'desc' => ''],
            'Disponible' => ['color' => 'bg-green-500', 'desc' => ''],            
            'Separacion Temporal' => ['color' => 'bg-blue-500', 'desc' => ''],
            'Separacion' => ['color' => 'bg-yellow-500', 'desc' => ''],
            'Pagado sin minuta' => ['color' => 'bg-indigo-500', 'desc' => ''],
            'Minuta' => ['color' => 'bg-gray-500', 'desc' => ''],
            'Cancelado' => ['color' => 'bg-green-500', 'desc' => ''],
            'Listo Entrega' => ['color' => 'bg-green-500', 'desc' => ''],
            'Entregado' => ['color' => 'bg-green-500', 'desc' => ''],
        ];

        return collect($estados)->map(function ($data, $nombre) {
            return [
                'nombre' => $nombre,
                'count' => Departamento::whereHas('estadoDepartamento', 
                    fn($q) => $q->where('nombre', $nombre)
                )->count(),
                'color' => $data['color'],
                'descripcion' => $data['desc']
            ];
        })->values()->all();
    }
}