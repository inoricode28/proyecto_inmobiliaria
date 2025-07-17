<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadosCivilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('estados_civiles')->insert([
            ['nombre' => 'Soltero(a)'],
            ['nombre' => 'Casado(a)'],
            ['nombre' => 'Divorciado(a)'],
            ['nombre' => 'Viudo(a)'],
            ['nombre' => 'Conviviente'],
        ]);
    }
}
