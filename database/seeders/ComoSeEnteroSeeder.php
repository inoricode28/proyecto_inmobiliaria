<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComoSeEnteroSeeder extends Seeder
{
    public function run()
    {
        $opciones = [
            [
                'nombre' => 'CYBER NEXO',
                'descripcion' => 'Conoció el proyecto a través de Cyber Nexo'
            ],
            [
                'nombre' => 'FACEBOOK',
                'descripcion' => 'Conoció el proyecto a través de Facebook'
            ],
            [
                'nombre' => 'FERIA INMOBILIARIA',
                'descripcion' => 'Conoció el proyecto en una feria inmobiliaria'
            ],
            [
                'nombre' => 'INSTAGRAM',
                'descripcion' => 'Conoció el proyecto a través de Instagram'
            ],
            [
                'nombre' => 'NEXO',
                'descripcion' => 'Conoció el proyecto a través de Nexo'
            ],
            [
                'nombre' => 'PAGINA WEB',
                'descripcion' => 'Conoció el proyecto a través de la página web'
            ],
            [
                'nombre' => 'PANEL PROYECTO',
                'descripcion' => 'Conoció el proyecto a través de paneles en el proyecto'
            ],
            [
                'nombre' => 'Plugins WhatsApp',
                'descripcion' => 'Conoció el proyecto a través de plugins de WhatsApp'
            ],
            [
                'nombre' => 'REFERIDO',
                'descripcion' => 'Fue referido por un conocido'
            ],
        ];

        foreach ($opciones as $opcion) {
            DB::table('como_se_entero')->insert($opcion);
        }
    }
}