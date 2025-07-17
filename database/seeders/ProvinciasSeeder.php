<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinciasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('provincias')->insert([
            ['nombre' => 'Lima', 'departamento_ubigeo_id' => 1],
            ['nombre' => 'Cañete', 'departamento_ubigeo_id' => 1],
            ['nombre' => 'Arequipa', 'departamento_ubigeo_id' => 2],
            ['nombre' => 'Cusco', 'departamento_ubigeo_id' => 3],
        ]);
    }
}
