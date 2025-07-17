<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradosEstudioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('grados_estudio')->insert([
            ['nombre' => 'Sin estudios'],
            ['nombre' => 'Primaria'],
            ['nombre' => 'Secundaria'],
            ['nombre' => 'Técnico'],
            ['nombre' => 'Universitario'],
            ['nombre' => 'Postgrado'],
        ]);
    }
}
