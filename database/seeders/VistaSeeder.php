<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VistaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('vistas')->insert([
            ['nombre' => 'Interno'],
            ['nombre' => 'Externo'],
        ]);
    }
}
