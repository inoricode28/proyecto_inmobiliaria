<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentosUbigeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('departamentos_ubigeo')->insert([
            ['nombre' => 'Lima'],
            ['nombre' => 'Arequipa'],
            ['nombre' => 'Cusco'],
        ]);
    }
}
