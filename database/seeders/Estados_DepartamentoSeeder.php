<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Importación necesaria añadida

class Estados_DepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $estados = [
            ['nombre' => 'Bloqueado', 'descripcion' => 'Departamento bloqueado temporalmente'],
            ['nombre' => 'Disponible', 'descripcion' => 'Departamento disponible para venta'],
            ['nombre' => 'Separacion Temporal', 'descripcion' => 'Separado temporalmente por cliente'],
            ['nombre' => 'Separacion', 'descripcion' => 'Separado definitivamente por cliente'],
            ['nombre' => 'Pagado sin minuta', 'descripcion' => 'Pagado sin minuta firmada'],
            ['nombre' => 'Minuta', 'descripcion' => 'Minuta firmada'],
            ['nombre' => 'Cancelado', 'descripcion' => 'Venta cancelada'],
            ['nombre' => 'Listo Entrega', 'descripcion' => 'Listo para entrega al cliente'],
            ['nombre' => 'Entregado', 'descripcion' => 'Entregado al cliente'],
        ];

        DB::table('estados_departamento')->insert($estados);
    }
}
