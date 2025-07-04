<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposFinanciamientoSeeder extends Seeder
{
    public function run()
    {
        $financiamientos = [
            ['nombre' => 'Crédito', 'descripcion' => 'Crédito para proyectos diversos', 'color' => '#4CAF50', 'is_default' => false],
            ['nombre' => 'Hipotecario', 'descripcion' => 'Crédito hipotecario para la compra de viviendas', 'color' => '#2196F3', 'is_default' => false],
            ['nombre' => 'Crédito Directo', 'descripcion' => 'Préstamos personales sin intermediarios', 'color' => '#FF9800', 'is_default' => false],
            ['nombre' => 'Contado', 'descripcion' => 'Pago de forma inmediata sin financiamiento', 'color' => '#F44336', 'is_default' => false],
            ['nombre' => 'Leasing', 'descripcion' => 'Arrendamiento de bienes con opción de compra', 'color' => '#9C27B0', 'is_default' => false],
            ['nombre' => 'Fovimar', 'descripcion' => 'Fondo de vivienda militar', 'color' => '#3F51B5', 'is_default' => false],
            ['nombre' => 'Fovipol', 'descripcion' => 'Fondo de vivienda para policías', 'color' => '#8BC34A', 'is_default' => false],
            ['nombre' => 'Permuta', 'descripcion' => 'Intercambio de propiedad entre dos partes', 'color' => '#FFEB3B', 'is_default' => false],
            ['nombre' => 'Ahorro', 'descripcion' => 'Ahorro personal para adquirir propiedades', 'color' => '#009688', 'is_default' => false],
            ['nombre' => 'Fovimfap', 'descripcion' => 'Fondo de vivienda para trabajadores del sector público', 'color' => '#607D8B', 'is_default' => false],
        ];

        foreach ($financiamientos as $financiamiento) {
            DB::table('tipos_financiamiento')->insert($financiamiento);
        }
    }
}
