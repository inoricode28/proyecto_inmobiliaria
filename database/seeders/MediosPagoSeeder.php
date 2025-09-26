<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediosPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('medios_pago')->insert([
            [
                'nombre' => 'DEPÓSITO EN CUENTA',
                'descripcion' => 'Depósito directo en cuenta bancaria',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'POSTVISA',
                'descripcion' => 'Pago con tarjeta PostVisa',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'TRANSFERENCIA BANCARIA',
                'descripcion' => 'Transferencia bancaria electrónica',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'CHEQUE DE GERENCIA',
                'descripcion' => 'Pago mediante cheque de gerencia',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'EFECTIVO',
                'descripcion' => 'Pago en efectivo',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
