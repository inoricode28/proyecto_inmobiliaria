<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormaContactoSeeder extends Seeder
{
    public function run()
    {
        $formasContacto = [
            [
                'nombre' => 'E-MAIL',
                'descripcion' => 'Contacto realizado por correo electrónico'
              
            ],
            [
                'nombre' => 'FACEBOOK',
                'descripcion' => 'Contacto realizado a través de Facebook'
                
            ],
            [
                'nombre' => 'FERIA INMOBILIARIA',
                'descripcion' => 'Contacto realizado en una feria inmobiliaria'
           
            ],
            [
                'nombre' => 'NEXO',
                'descripcion' => 'Contacto realizado a través de Nexo'
              
            ],
            [
                'nombre' => 'PAGINA WEB',
                'descripcion' => 'Contacto realizado a través de la página web'
               
            ],
            [
                'nombre' => 'Plugins WhatsApp',
               
            ],
            [
                'nombre' => 'SALA DE VENTA',
                'descripcion' => 'Contacto realizado en sala de ventas'
                
            ],
            [
                'nombre' => 'TELEFÓNICO',
                'descripcion' => 'Contacto realizado por llamada telefónica'
              
            ],
            [
                'nombre' => 'WHATSAPP',
                'descripcion' => 'Contacto realizado a través de WhatsApp'
                
            ],
        ];

        foreach ($formasContacto as $forma) {
            DB::table('formas_contacto')->insert($forma);
        }
    }
}