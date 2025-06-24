<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonedaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('moneda')->insert([
            ['nombre' => 'Soles'],
            ['nombre' => 'Dólares'],
        ]);
    }
}
