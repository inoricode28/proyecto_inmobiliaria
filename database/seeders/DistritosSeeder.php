<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistritosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('distritos')->insert([
            ['nombre' => 'Miraflores', 'provincia_id' => 1],
            ['nombre' => 'San Isidro', 'provincia_id' => 1],
            ['nombre' => 'Asia', 'provincia_id' => 2],
            ['nombre' => 'Yanahuara', 'provincia_id' => 3],
            ['nombre' => 'Wanchaq', 'provincia_id' => 4],
        ]);
    }
}
