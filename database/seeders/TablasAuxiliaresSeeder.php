<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TablasAuxiliaresSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            GenerosSeeder::class,
            EstadosCivilesSeeder::class,
            GradosEstudioSeeder::class,
            NacionalidadesSeeder::class,
            DepartamentosUbigeoSeeder::class,
            ProvinciasSeeder::class,
            DistritosSeeder::class,
        ]);
    }
}

