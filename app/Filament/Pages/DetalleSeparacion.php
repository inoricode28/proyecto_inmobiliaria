<?php

namespace App\Filament\Pages;

use App\Models\Proforma;
use App\Models\Separacion;
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
    public $tieneVenta; // Nueva propiedad
    
    public function mount(Request $request)
    {
        $proformaId = $request->route('proforma_id');
        
        // Buscar la proforma con todas las relaciones necesarias
        $this->proforma = Proforma::with([
            'separacion',
            'separacion.venta', // Agregar relación con venta
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
        
        if (!$this->proforma->separacion) {
            abort(404, 'No se encontró información de separación para esta proforma');
        }

        $this->departamento = $this->proforma->departamento;
        $this->separacion = $this->proforma->separacion;
        
        // Verificar si la separación tiene una venta asociada
        $this->tieneVenta = $this->separacion->venta !== null;
    }
    
    protected function getViewData(): array
    {
        return [
            'proforma' => $this->proforma,
            'separacion' => $this->separacion,
            'departamento' => $this->departamento,
            'tieneVenta' => $this->tieneVenta, // Pasar a la vista
        ];
    }
}