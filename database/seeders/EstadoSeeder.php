<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insertar los estados activos e inactivos
        DB::table('estado')->insert([
            [
                'activo' => true,  // 1: Activo
            ],
            [
                'activo' => false,  // 0: Inactivo
            ]
        ]);
    }
}
