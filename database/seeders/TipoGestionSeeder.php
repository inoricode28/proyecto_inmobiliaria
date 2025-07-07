<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoGestionSeeder extends Seeder
{
    public function run()
    {
        $tiposGestion = [
            [
                'nombre' => 'No gestionado',
                'descripcion' => 'Prospecto que aún no ha sido contactado',
                'color' => '#9CA3AF' // Gris
            ],
            [
                'nombre' => 'Por Contactar',
                'descripcion' => 'Prospecto en lista para ser contactado',
                'color' => '#3B82F6' // Azul
            ],
            [
                'nombre' => 'Contactados',
                'descripcion' => 'Prospecto que ha sido contactado',
                'color' => '#6366F1' // Índigo
            ],
            [
                'nombre' => 'Citados',
                'descripcion' => 'Prospecto con cita programada',
                'color' => '#8B5CF6' // Violeta
            ],
            [
                'nombre' => 'Visitas',
                'descripcion' => 'Prospecto que realizó visita al proyecto',
                'color' => '#EC4899' // Rosa
            ],
            [
                'nombre' => 'Separaciones',
                'descripcion' => 'Prospecto que ha separado una unidad',
                'color' => '#10B981' // Verde
            ],
        ];

        foreach ($tiposGestion as $tipo) {
            DB::table('tipos_gestion')->insert($tipo);
        }
    }
}