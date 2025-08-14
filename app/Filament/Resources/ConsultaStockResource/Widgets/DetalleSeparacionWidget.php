<?php

namespace App\Filament\Resources\ConsultaStockResource\Widgets;

use App\Models\Departamento;
use App\Models\Separacion;
use App\Models\Proforma;
use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class DetalleSeparacionWidget extends Widget
{
    protected static string $view = 'filament.resources.consulta-stock-resource.detalle-separacion-widget';
    
    protected int|string|array $columnSpan = 'full';
    
    public $proformaId;
    public $separacionData;
    public $proformaData;
    public $visitaData;
    public $modalAbierto = false;

    public static function canView(): bool
    {
        return true;
    }

    public function mount($proformaId = null)
    {
        $this->proformaId = $proformaId;
        $this->loadSeparacionData();
    }
    
    public function abrirModal($proformaId)
    {
        $this->proformaId = $proformaId;
        $this->loadSeparacionData();
        $this->modalAbierto = true;
        $this->dispatch('modal-abierto');
    }
    
    public function cerrarModal()
    {
        $this->modalAbierto = false;
        $this->proformaId = null;
        $this->separacionData = null;
        $this->proformaData = null;
        $this->visitaData = null;
    }
    
    protected function loadSeparacionData()
    {
        if (!$this->proformaId) return;
        
        $proforma = Proforma::with([
            'separacion',
            'proyecto',
            'departamento.estadoDepartamento'
        ])->find($this->proformaId);
        
        if (!$proforma) {
            return;
        }
        
        $this->proformaData = $proforma;
        
        if ($proforma->separacion) {
            $this->separacionData = $proforma->separacion;
        } else {
            // Si no hay separación, crear datos básicos
            $this->separacionData = (object) [
                'id' => 0,
                'tipo_separacion' => 'Separación Temporal',
                'ruc' => 'N/A',
                'empresa' => 'N/A',
                'ingresos' => 0,
                'saldo_a_financiar' => $proforma->departamento->precio * 0.9,
                'created_at' => now()
            ];
        }
        
        // Datos de visita
        $this->visitaData = [
            'fecha_visita' => $proforma->created_at->format('d/m/Y H:i'),
            'fecha_proforma' => $proforma->created_at->format('d/m/Y H:i'),
            'fecha_vencimiento' => $proforma->created_at->addDays(15)->format('d/m/Y H:i'),
        ];
    }
    
    protected function getViewData(): array
    {
        return [
            'proformaData' => $this->proformaData,
            'separacionData' => $this->separacionData,
            'visitaData' => $this->visitaData,
            'proformaId' => $this->proformaId,
            'modalAbierto' => $this->modalAbierto
        ];
    }
}