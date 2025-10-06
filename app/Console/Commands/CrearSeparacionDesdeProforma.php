<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Proforma;
use App\Models\Separacion;
use App\Models\EstadoDepartamento;
use App\Models\ProformaInmueble;
use App\Models\Departamento;
use Illuminate\Support\Facades\Log;

class CrearSeparacionDesdeProforma extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'separacion:crear {proforma_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear (o reutilizar) una separación a partir de una proforma existente';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $proformaId = (int) $this->argument('proforma_id');

        $proforma = Proforma::find($proformaId);
        if (!$proforma) {
            $this->error('Proforma no encontrada: ' . $proformaId);
            return self::FAILURE;
        }

        $separacion = Separacion::where('proforma_id', $proformaId)->first();
        if (!$separacion) {
            // Determinar usuario para auditoría si existe alguno
            $userId = \App\Models\User::query()->value('id') ?? null;

            $separacion = Separacion::create([
                'proforma_id' => $proformaId,
                // Campos opcionales comunes
                'saldo_a_financiar' => $proforma->saldo_financiar ?? null,
                'fecha_vencimiento' => now()->addDays(30),
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);
            $this->info('Separación creada ID: ' . $separacion->id);
        } else {
            $this->info('Separación ya existe ID: ' . $separacion->id);
        }

        if ($proforma->prospecto) {
            $proforma->prospecto->update([
                'tipo_gestion_id' => 6,
            ]);
            $this->info('Prospecto actualizado a estado separación (tipo_gestion_id=6).');
        }

        // Actualizar estado de departamentos vinculados a la proforma
        $estadoSeparacion = EstadoDepartamento::where('nombre', 'Separacion')->first();
        if (!$estadoSeparacion) {
            $this->warn('Estado "Separacion" no encontrado en estados_departamento. No se actualizaron inmuebles.');
        } else {
            // Obtener todos los departamentos asociados a la proforma vía proforma_inmuebles
            $departamentoIds = ProformaInmueble::where('proforma_id', $proformaId)
                ->pluck('departamento_id')
                ->filter()
                ->unique()
                ->values();

            // Fallback al departamento principal si no hay registros en proforma_inmuebles
            if ($departamentoIds->isEmpty() && $proforma->departamento_id) {
                $departamentoIds = collect([$proforma->departamento_id]);
            }

            if ($departamentoIds->isEmpty()) {
                $this->warn('La proforma no tiene departamentos asociados para actualizar estado.');
            } else {
                $actualizados = 0;
                foreach ($departamentoIds as $depId) {
                    $departamento = Departamento::find($depId);
                    if (!$departamento) {
                        $this->warn("Departamento no encontrado (ID: {$depId})");
                        continue;
                    }

                    $departamento->estado_departamento_id = $estadoSeparacion->id;
                    $departamento->vendible = false;
                    $departamento->save();
                    $actualizados++;

                    $this->info("Departamento {$departamento->num_departamento} (ID: {$departamento->id}) actualizado a estado 'Separacion' y marcado no vendible.");
                }

                if ($actualizados > 0) {
                    $this->info("Total de departamentos actualizados: {$actualizados}");
                }
            }
        }

        return self::SUCCESS;
    }
}