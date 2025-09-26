<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cronograma_cuota_inicial', function (Blueprint $table) {
            // Agregar columna proforma_id
            if (!Schema::hasColumn('cronograma_cuota_inicial', 'proforma_id')) {
                $table->foreignId('proforma_id')->nullable()->after('separacion_id')->constrained('proformas')->onDelete('cascade');
            }
            
            // Modificar separacion_id para que sea nullable
            $table->foreignId('separacion_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cronograma_cuota_inicial', function (Blueprint $table) {
            // Eliminar columna proforma_id
            if (Schema::hasColumn('cronograma_cuota_inicial', 'proforma_id')) {
                $table->dropForeign(['proforma_id']);
                $table->dropColumn('proforma_id');
            }
            
            // Revertir separacion_id a no nullable
            $table->foreignId('separacion_id')->nullable(false)->change();
        });
    }
};