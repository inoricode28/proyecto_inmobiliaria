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
    public $inmuebles;

    public function mount(Request $request)
    {
        $proformaId = $request->route('proforma_id');
        $departamentoId = $request->query('departamento_id');

        // Buscar la proforma con todas las relaciones necesarias
        $this->proforma = Proforma::with([
            'separacion',
            'separacion.venta',
            'separacion.venta.entrega', // Agregar relación con entrega
            'departamento.estadoDepartamento',
            'departamento.tipoFinanciamiento',
            'departamento.proyecto',
            // Eager load de inmuebles de la proforma y sus relaciones
            'proformaInmuebles',
            'proformaInmuebles.departamento',
            'proformaInmuebles.departamento.proyecto',
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
        $this->separacion = $this->proforma->separacion; // Puede ser null
        // Inmuebles vinculados a la proforma (principal y adicionales)
        $this->inmuebles = $this->proforma->proformaInmuebles;

        // Selección de departamento:
        // 1) Si llega departamento_id, priorizarlo buscando en proformaInmuebles o separacion_inmuebles
        // 2) Si no, usar el principal de proforma o el departamento directamente
        if ($departamentoId) {
            $inmuebleSeleccionado = null;

            // Buscar primero en proformaInmuebles
            if ($this->proforma->proformaInmuebles && $this->proforma->proformaInmuebles->count() > 0) {
                $inmuebleSeleccionado = $this->proforma->proformaInmuebles
                    ->firstWhere('departamento_id', (int) $departamentoId);
            }

            // Si no se encuentra, intentar en separación_inmuebles
            if (!$inmuebleSeleccionado && $this->separacion && $this->separacion->inmuebles) {
                $inmuebleSeleccionado = $this->separacion->inmuebles
                    ->firstWhere('departamento_id', (int) $departamentoId);
            }

            // Si existe, usar su departamento
            if ($inmuebleSeleccionado && $inmuebleSeleccionado->departamento) {
                $this->departamento = $inmuebleSeleccionado->departamento;
            } else {
                // Fallback a buscar directamente el departamento por ID
                $this->departamento = \App\Models\Departamento::find($departamentoId) ?? $this->proforma->departamento;
            }
        } else {
            // Sin parámetro, mantener el comportamiento actual
            $this->departamento = $this->proforma->departamento;
        }

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
            'inmuebles' => $this->inmuebles,
        ];
    }
}
