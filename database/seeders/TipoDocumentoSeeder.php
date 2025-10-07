<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoDocumentoSeeder extends Seeder
{
    public function run()
    {
        $tiposDocumento = [
            [
                'nombre' => 'Carnet de Extranjería',
                'descripcion' => 'Documento de identidad para extranjeros residentes',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Carnet Diplomático',
                'descripcion' => 'Documento para personal diplomático',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'CI',
                'descripcion' => 'Cédula de Identidad (para algunos países)',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'DNI',
                'descripcion' => 'Documento Nacional de Identidad',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'INDOCUMENTADO',
                'descripcion' => 'Persona que no posee documento de identidad',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'PASAPORTE',
                'descripcion' => 'Documento de identidad para viajes internacionales',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'RUC',
                'descripcion' => 'Registro Único de Contribuyentes',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        foreach ($tiposDocumento as $tipo) {
            DB::table('tipo_documento')->updateOrInsert(
                ['nombre' => $tipo['nombre']],
                $tipo
            );
        }
    }
}