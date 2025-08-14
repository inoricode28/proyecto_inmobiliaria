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
    
    public $departamentoId;
    public $separacionData;
    public $proformaData;
    public $visitaData;
    public $modalAbierto = false;

    public static function canView(): bool
    {
        return true;
    }

    public function mount($departamentoId = null)
    {
        $this->departamentoId = $departamentoId;
        $this->loadSeparacionData();
    }
    
    public function abrirModal($departamentoId)
    {
        $this->departamentoId = $departamentoId;
        $this->loadSeparacionData();
        $this->modalAbierto = true;
        $this->dispatch('modal-abierto');
    }
    
    public function cerrarModal()
    {
        $this->modalAbierto = false;
        $this->departamentoId = null;
        $this->separacionData = null;
        $this->proformaData = null;
        $this->visitaData = null;
    }
    
    protected function loadSeparacionData()
    {
        if (!$this->departamentoId) return;
        
        $departamento = Departamento::with([
            'proformas.separacion',
            'estadoDepartamento',
            'proyecto',
            'tipoFinanciamiento'
        ])->find($this->departamentoId);
        
        if (!$departamento) {
            return;
        }
        
        // Buscar cualquier estado que contenga "Separacion"
        $esSeparacion = str_contains(strtolower($departamento->estadoDepartamento->nombre), 'separacion');
        
        if (!$esSeparacion) {
            return;
        }
        
        // Obtener la proforma más reciente con separación
        $proforma = $departamento->proformas()
            ->whereHas('separacion')
            ->with(['separacion', 'proyecto', 'departamento'])
            ->latest()
            ->first();
            
        if ($proforma && $proforma->separacion) {
            $this->proformaData = $proforma;
            $this->separacionData = $proforma->separacion;
            
            // Datos de visita
            $this->visitaData = [
                'fecha_visita' => $proforma->created_at->format('d/m/Y H:i'),
                'fecha_proforma' => $proforma->created_at->format('d/m/Y H:i'),
                'fecha_vencimiento' => $proforma->created_at->addDays(15)->format('d/m/Y H:i'),
            ];
        } else {
            // Si no hay proforma con separación, crear datos básicos
            $this->proformaData = (object) [
                'nombres' => 'Sin datos',
                'ape_paterno' => '',
                'proyecto' => $departamento->proyecto,
                'departamento' => $departamento,
                'monto_separacion' => $departamento->precio * 0.1, // 10% como ejemplo
                'created_at' => now()
            ];
            
            $this->separacionData = (object) [
                'id' => 0,
                'tipo_separacion' => 'Separación Temporal',
                'ruc' => 'N/A',
                'empresa' => 'N/A',
                'ingresos' => 0,
                'saldo_a_financiar' => $departamento->precio * 0.9,
                'created_at' => now()
            ];
            
            $this->visitaData = [
                'fecha_visita' => now()->format('d/m/Y H:i'),
                'fecha_proforma' => now()->format('d/m/Y H:i'),
                'fecha_vencimiento' => now()->addDays(15)->format('d/m/Y H:i'),
            ];
        }
    }
    
    protected function getViewData(): array
    {
        return [
            'proformaData' => $this->proformaData,
            'separacionData' => $this->separacionData,
            'visitaData' => $this->visitaData,
            'departamentoId' => $this->departamentoId,
            'modalAbierto' => $this->modalAbierto
        ];
    }
}