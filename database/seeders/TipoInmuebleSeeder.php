<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoInmuebleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tipo_inmueble')->insert([
            ['nombre' => 'Departamento'],
            ['nombre' => 'Estacionamiento'],
            ['nombre' => 'Estacionamiento de bicicletas'],
        ]);
    }
}
