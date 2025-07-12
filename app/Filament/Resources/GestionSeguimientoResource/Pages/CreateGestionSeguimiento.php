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
    $tipoIndocumentado = \App\Models\TipoDocumento::where('nombre', 'INDOCUMENTADO')->value('id');
    $tipoRuc = \App\Models\TipoDocumento::where('nombre', 'RUC')->value('id');

        $data['tipo_gestion_id'] = 1;

    // Limpiar datos según tipo de documento
    $numeroDocumento = $data['tipo_documento_id'] == $tipoIndocumentado ? null : $data['numero_documento'];
    $razonSocial = $data['tipo_documento_id'] == $tipoRuc ? $data['razon_social'] : null;
    $nombres = in_array($data['tipo_documento_id'], [$tipoIndocumentado, $tipoDni = \App\Models\TipoDocumento::where('nombre', 'DNI')->value('id')])
        ? $data['nombres'] : null;
    $apePaterno = in_array($data['tipo_documento_id'], [$tipoIndocumentado, $tipoDni])
        ? $data['ape_paterno'] : null;
    $apeMaterno = in_array($data['tipo_documento_id'], [$tipoIndocumentado, $tipoDni])
        ? $data['ape_materno'] : null;

    $prospecto = Prospecto::create([
        'fecha_registro' => $data['prospecto_fecha_registro'],
        'tipo_documento_id' => $data['tipo_documento_id'],
        'numero_documento' => $numeroDocumento,
        'razon_social' => $razonSocial,
        'nombres' => $nombres,
        'ape_paterno' => $apePaterno,
        'ape_materno' => $apeMaterno,
        'celular' => $data['celular'],
        'correo_electronico' => $data['correo_electronico'] ?? null,
        'proyecto_id' => $data['proyecto_id'],
        'tipo_inmueble_id' => $data['tipo_inmueble_id'],
        'forma_contacto_id' => $data['prospecto_forma_contacto_id'],
        'como_se_entero_id' => $data['como_se_entero_id'],
        'tipo_gestion_id' => $data['tipo_gestion_id'],
        'created_by' => Auth::id(),
        'updated_by' => Auth::id(),
    ]);

    // Crear la primera tarea asociada
    Tarea::create([
        'prospecto_id' => $prospecto->id,
        'forma_contacto_id' => $data['tarea_forma_contacto_id'],
        'nivel_interes_id' => $data['nivel_interes_id'],
        'usuario_asignado_id' => $data['usuario_asignado_id'],
        'fecha_realizar' => $data['tarea_fecha_realizar'],
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
