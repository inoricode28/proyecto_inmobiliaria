<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ← ESTA LÍNEA ES LA CLAVE

class TiposDepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::table('tipos_departamento')->insert([
            ['nombre' => 'FLAT', 'descripcion' => 'FLAT'],
            ['nombre' => 'DUPLEX', 'descripcion' => 'DUPLEX'],
            ['nombre' => 'TRIPLEX', 'descripcion' => 'TRIPLEX'],
        ]);
    }
}
