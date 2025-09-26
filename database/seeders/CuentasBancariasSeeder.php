<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CuentasBancariasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cuentas_bancarias')->insert([
            [
                'banco' => 'BanBif S/.',
                'numero_cuenta' => '700-0704500',
                'tipo_cuenta' => 'Corriente',
                'moneda' => 'PEN',
                'titular' => 'GRUPO VICTORIA',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'banco' => 'BanBif S/.',
                'numero_cuenta' => 'CCI 038-101-107000704500-20',
                'tipo_cuenta' => 'CCI',
                'moneda' => 'PEN',
                'titular' => 'GRUPO VICTORIA',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'banco' => 'BCP S/.',
                'numero_cuenta' => '002-193-009948658092-13',
                'tipo_cuenta' => 'Corriente',
                'moneda' => 'PEN',
                'titular' => 'GRUPO VICTORIA',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
