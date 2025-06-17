<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */    

public function run()
{
    DB::table('departamentos')->insert([
        ['nombre' => 'Lima', 'estado' => true],
        ['nombre' => 'Cusco', 'estado' => true],
        ['nombre' => 'Arequipa', 'estado' => false],
    ]);
}

}
