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
        Schema::table('cronogramas_saldo_financiar', function (Blueprint $table) {
            // Agregar proforma_id
            $table->foreignId('proforma_id')->nullable()->constrained('proformas')->onDelete('cascade');
            
            // Hacer separacion_id nullable
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
        Schema::table('cronogramas_saldo_financiar', function (Blueprint $table) {
            // Eliminar proforma_id
            $table->dropForeign(['proforma_id']);
            $table->dropColumn('proforma_id');
            
            // Revertir separacion_id a no nullable
            $table->foreignId('separacion_id')->nullable(false)->change();
        });
    }
};
