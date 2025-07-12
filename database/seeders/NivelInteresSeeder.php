<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NivelInteresSeeder extends Seeder
{
    public function run()
    {
        $nivelesInteres = [
            [
                'nombre' => 'BAJO',
                'descripcion' => 'Prospecto con bajo nivel de interés',
                'color' => '#9CA3AF' // Gris
            ],
            [
                'nombre' => 'DERIVADO BANCO',
                'descripcion' => 'Prospecto derivado al banco',
                'color' => '#3B82F6' // Azul
            ],
            [
                'nombre' => 'SEGUIMIENTO',
                'descripcion' => 'Prospecto en seguimiento',
                'color' => '#8B5CF6' // Violeta
            ],
            [
                'nombre' => 'INTERESADO',
                'descripcion' => 'Prospecto interesado',
                'color' => '#EC4899' // Rosa
            ],
            [
                'nombre' => 'COLOMBIA',
                'descripcion' => 'Prospecto relacionado con Colombia',
                'color' => '#10B981' // Verde
            ],
            [
                'nombre' => 'PRE CALIFICACION',
                'descripcion' => 'Prospecto en pre calificación',
                'color' => '#F59E0B' // Amarillo
            ],
            [
                'nombre' => 'POTENCIAL',
                'descripcion' => 'Prospecto potencial',
                'color' => '#EF4444' // Rojo
            ],
        ];

        foreach ($nivelesInteres as $nivel) {
            DB::table('niveles_interes')->insert($nivel);
        }
    }
}