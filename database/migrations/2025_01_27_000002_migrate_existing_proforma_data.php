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
        // Verificar si la tabla proforma_inmuebles existe y tiene datos
        if (Schema::hasTable('proforma_inmuebles') && DB::table('proforma_inmuebles')->count() == 0) {
            // Migrar datos existentes de proformas a la nueva tabla proforma_inmuebles
            DB::statement("
                INSERT INTO proforma_inmuebles (
                    proforma_id, 
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
                    id as proforma_id,
                    departamento_id,
                    COALESCE((SELECT Precio_lista FROM departamentos WHERE id = proformas.departamento_id), 0) as precio_lista,
                    0 as precio_venta,
                    0 as descuento,
                    COALESCE(monto_separacion, 0) as monto_separacion,
                    COALESCE(monto_cuota_inicial, 0) as monto_cuota_inicial,
                    1 as orden,
                    true as es_principal,
                    created_at,
                    updated_at
                FROM proformas 
                WHERE departamento_id IS NOT NULL
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
        // Limpiar la tabla proforma_inmuebles
        DB::table('proforma_inmuebles')->truncate();
    }
};