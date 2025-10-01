<?php

namespace App\Filament\Pages;

use App\Models\Proforma;
use App\Models\Separacion;
use App\Models\Entrega;
use Filament\Pages\Page;
use Illuminate\Http\Request;

class DetalleSeparacion extends Page
{
    protected static string $view = 'filament.pages.detalle-separacion';
    
    protected static ?string $title = 'Detalle de Separación';
    
    protected static bool $shouldRegisterNavigation = false;
    
    public $proforma;
    public $separacion;
    public $departamento;
    public $tieneVenta;
    public $entregaExistente;
    
    public function mount(Request $request)
    {
        $proformaId = $request->route('proforma_id');
        
        // Buscar la proforma con todas las relaciones necesarias
        $this->proforma = Proforma::with([
            'separacion',
            'separacion.venta',
            'separacion.venta.entrega', // Agregar relación con entrega
            'departamento.estadoDepartamento',
            'departamento.tipoFinanciamiento',
            'departamento.proyecto',
            'prospecto',
            'tipoDocumento',
            'genero',
            'estadoCivil',
            'gradoEstudio',
            'nacionalidad',
            'separacion.notariaKardex',
            'separacion.cartaFianza'
        ])->find($proformaId);
        
        if (!$this->proforma) {
            abort(404, 'No se encontró la proforma especificada');
        }

        // Permitir acceso sin importar si tiene separación o no
        $this->departamento = $this->proforma->departamento;
        $this->separacion = $this->proforma->separacion; // Puede ser null
        
        // Verificar si la separación tiene una venta asociada
        $this->tieneVenta = $this->separacion && $this->separacion->venta !== null;
        
        // Verificar si ya existe una entrega para esta venta
        if ($this->tieneVenta && $this->separacion->venta) {
            $this->entregaExistente = Entrega::where('venta_id', $this->separacion->venta->id)->first();
        }
    }
    
    protected function getViewData(): array
    {
        return [
            'proforma' => $this->proforma,
            'separacion' => $this->separacion,
            'departamento' => $this->departamento,
            'tieneVenta' => $this->tieneVenta,
            'entregaExistente' => $this->entregaExistente,
        ];
    }
}