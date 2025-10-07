<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Verificar si la tabla separacion_inmuebles existe y está vacía
        if (Schema::hasTable('separacion_inmuebles') && DB::table('separacion_inmuebles')->count() == 0) {
            // Verificar qué columnas existen en la tabla proformas
            $hasDescuento = Schema::hasColumn('proformas', 'descuento');
            $hasPrecioVenta = Schema::hasColumn('proformas', 'precio_venta');
            
            // Construir la consulta dinámicamente según las columnas disponibles
            $precioVentaSelect = $hasPrecioVenta ? 'COALESCE(p.precio_venta, 0)' : '0';
            $descuentoSelect = $hasDescuento ? 'COALESCE(p.descuento, 0)' : '0';
            
            // Migrar datos existentes de separaciones a la nueva tabla separacion_inmuebles
            // Tomamos los datos de la proforma asociada a cada separación
            DB::statement("
                INSERT INTO separacion_inmuebles (
                    separacion_id, 
                    departamento_id, 
                    precio_lista, 
                    precio_venta, 
                    descuento, 
                    monto_separacion, 
                    monto_cuota_inicial, 
                    orden, 
                    es_principal, 
                    created_at, 
                    updated_at
                )
                SELECT 
                    s.id as separacion_id,
                    p.departamento_id,
                    COALESCE((SELECT Precio_lista FROM departamentos WHERE id = p.departamento_id), 0) as precio_lista,
                    {$precioVentaSelect} as precio_venta,
                    {$descuentoSelect} as descuento,
                    COALESCE(p.monto_separacion, 0) as monto_separacion,
                    COALESCE(p.monto_cuota_inicial, 0) as monto_cuota_inicial,
                    1 as orden,
                    true as es_principal,
                    s.created_at,
                    s.updated_at
                FROM separaciones s 
                INNER JOIN proformas p ON s.proforma_id = p.id
                WHERE p.departamento_id IS NOT NULL
            ");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Limpiar la tabla separacion_inmuebles
        DB::table('separacion_inmuebles')->truncate();
    }
};