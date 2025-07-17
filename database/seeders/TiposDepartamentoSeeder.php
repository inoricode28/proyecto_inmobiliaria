<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ← ESTA LÍNEA ES LA CLAVE

class TiposDepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'FLAT', 'descripcion' => 'FLAT'],
            ['nombre' => 'DUPLEX', 'descripcion' => 'DUPLEX'],
            ['nombre' => 'TRIPLEX', 'descripcion' => 'TRIPLEX'],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tipos_departamento')->updateOrInsert(
                ['nombre' => $tipo['nombre']],
                $tipo
            );
        }
    }
}
