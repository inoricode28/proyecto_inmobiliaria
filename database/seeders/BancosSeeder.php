<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BancosSeeder extends Seeder
{
    public function run()
    {
        $bancos = [
            ['nombre' => 'Banco de Crédito del Perú (BCP)', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'BBVA Continental', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Scotiabank Perú', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Interbank', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Banco de la Nación', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Banco Pichincha', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Mibanco', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Banco Falabella', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Banco Ripley', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Banco Santander Perú', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Banco GNB', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Citibank', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Banco Azteca', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'ICBC Perú Bank', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($bancos as $banco) {
            DB::table('bancos')->insert($banco);
        }
    }
}