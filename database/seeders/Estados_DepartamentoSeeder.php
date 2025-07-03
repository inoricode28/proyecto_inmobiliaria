<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
            [
                'nombre' => 'Bloqueado',
                'descripcion' => 'Departamento bloqueado temporalmente',
                'color' => '#ef4444', // Rojo
                'is_default' => false
            ],
            [
                'nombre' => 'Disponible',
                'descripcion' => 'Departamento disponible para venta',
                'color' => '#10b981', // Verde
                'is_default' => true // Estado por defecto
            ],
            [
                'nombre' => 'Separacion Temporal',
                'descripcion' => 'Separado temporalmente por cliente',
                'color' => '#f59e0b', // Amarillo
                'is_default' => false
            ],
            [
                'nombre' => 'Separacion',
                'descripcion' => 'Separado definitivamente por cliente',
                'color' => '#f97316', // Naranja
                'is_default' => false
            ],
            [
                'nombre' => 'Pagado sin minuta',
                'descripcion' => 'Pagado sin minuta firmada',
                'color' => '#8b5cf6', // Violeta
                'is_default' => false
            ],
            [
                'nombre' => 'Minuta',
                'descripcion' => 'Minuta firmada',
                'color' => '#6366f1', // Índigo
                'is_default' => false
            ],
            [
                'nombre' => 'Cancelado',
                'descripcion' => 'Venta cancelada',
                'color' => '#64748b', // Gris
                'is_default' => false
            ],
            [
                'nombre' => 'Listo Entrega',
                'descripcion' => 'Listo para entrega al cliente',
                'color' => '#06b6d4', // Cian
                'is_default' => false
            ],
            [
                'nombre' => 'Entregado',
                'descripcion' => 'Entregado al cliente',
                'color' => '#14b8a6', // Verde azulado
                'is_default' => false
            ],
        ];

        DB::table('estados_departamento')->insert($estados);
    }
}
