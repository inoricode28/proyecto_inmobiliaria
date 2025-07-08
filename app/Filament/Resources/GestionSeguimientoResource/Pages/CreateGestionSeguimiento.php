<?php

namespace App\Filament\Resources\GestionSeguimientoResource\Pages;

use App\Filament\Resources\GestionSeguimientoResource;
use App\Models\Prospecto;
use App\Models\Tarea;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateGestionSeguimiento extends CreateRecord
{
    protected static string $resource = GestionSeguimientoResource::class;

    protected function handleRecordCreation(array $data): Prospecto
    {
        if (empty($data['forma_contacto_id'])) {
            throw ValidationException::withMessages([
                'data.forma_contacto_id' => 'Debe seleccionar una Forma de Contacto',
            ]);
        }

        if (empty($data['nivel_interes_id'])) {
            throw ValidationException::withMessages([
                'data.nivel_interes_id' => 'Debe seleccionar un Nivel de Interés',
            ]);
        }

        // Crear el prospecto
        $prospecto = Prospecto::create([
            'fecha_registro' => $data['fecha_registro'],
            'tipo_documento_id' => $data['tipo_documento_id'],
            'numero_documento' => $data['numero_documento'],
            'nombres' => $data['nombres'],
            'ape_paterno' => $data['ape_paterno'],
            'ape_materno' => $data['ape_materno'] ?? null,
            'celular' => $data['celular'],
            'correo_electronico' => $data['correo_electronico'] ?? null,
            'proyecto_id' => $data['proyecto_id'],
            'tipo_inmueble_id' => $data['tipo_inmueble_id'],
            'forma_contacto_id' => $data['forma_contacto_id'],
            'como_se_entero_id' => $data['como_se_entero_id'],
            'tipo_gestion_id' => $data['tipo_gestion_id'],
            'derivado_banco' => $data['derivado_banco'] ?? 'POTENCIAL',
            'pre_calificacion' => $data['pre_calificacion'] ?? null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Crear la primera tarea asociada
        Tarea::create([
            'prospecto_id' => $prospecto->id,
            'forma_contacto_id' => $data['forma_contacto_id'],
            'nivel_interes_id' => $data['nivel_interes_id'],
            'usuario_asignado_id' => $data['usuario_asignado_id'],
            'fecha_realizar' => $data['fecha_realizar'],
            'hora' => $data['hora_seguimiento'],
            'nota' => $data['nota'] ?? null,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return $prospecto;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Prospecto registrado con éxito';
    }

    protected function getCreatedNotificationMessage(): ?string
    {
        return 'El prospecto y su primera tarea han sido registrados correctamente';
    }
}
