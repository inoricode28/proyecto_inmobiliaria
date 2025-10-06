<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Proforma;

class MostrarInmueblesProforma extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proforma:inmuebles {proforma_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Muestra los inmuebles y sus estados de una proforma';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $id = (int) $this->argument('proforma_id');
        $proforma = Proforma::with(['inmuebles.departamento.estadoDepartamento', 'departamento.estadoDepartamento'])->find($id);

        if (!$proforma) {
            $this->error("Proforma no encontrada: {$id}");
            return 1;
        }

        $rows = [];

        foreach ($proforma->inmuebles as $pi) {
            $departamento = $pi->departamento;
            $estadoDepto = $departamento ? $departamento->estadoDepartamento : null;
            $rows[] = [
                'proforma_inmueble_id' => $pi->id,
                'departamento_id' => $departamento ? $departamento->id : null,
                'num_departamento' => $departamento ? $departamento->num_departamento : null,
                'estado' => $estadoDepto ? $estadoDepto->nombre : null,
                'color' => $estadoDepto ? $estadoDepto->color : null,
                'vendible' => $departamento ? ($departamento->vendible ? 'Sí' : 'No') : null,
                'precio' => $departamento ? $departamento->precio : null,
            ];
        }

        // Fallback: si no hay registros en proforma_inmuebles, usar el departamento directo de la proforma
        if (empty($rows)) {
            $departamento = $proforma->departamento;
            $estadoDepto = $departamento ? $departamento->estadoDepartamento : null;
            if ($departamento) {
                $rows[] = [
                    'proforma_inmueble_id' => null,
                    'departamento_id' => $departamento->id,
                    'num_departamento' => $departamento->num_departamento,
                    'estado' => $estadoDepto ? $estadoDepto->nombre : null,
                    'color' => $estadoDepto ? $estadoDepto->color : null,
                    'vendible' => $departamento->vendible ? 'Sí' : 'No',
                    'precio' => $departamento->precio,
                ];
            }
        }

        if (empty($rows)) {
            $this->info('La proforma no tiene inmuebles asociados.');
            return 0;
        }

        $this->table(
            ['ProformaInmuebleID', 'DepartamentoID', 'NumDep', 'Estado', 'Color', 'Vendible', 'Precio'],
            array_map(function ($r) {
                return [
                    $r['proforma_inmueble_id'],
                    $r['departamento_id'],
                    $r['num_departamento'],
                    $r['estado'],
                    $r['color'],
                    $r['vendible'],
                    $r['precio'],
                ];
            }, $rows)
        );

        return 0;
    }
}
