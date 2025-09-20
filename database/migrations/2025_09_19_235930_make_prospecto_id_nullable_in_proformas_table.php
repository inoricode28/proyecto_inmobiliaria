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
        Schema::table('proformas', function (Blueprint $table) {
            // Eliminar la restricción de clave foránea existente
            $table->dropForeign(['prospecto_id']);
            
            // Hacer nullable el campo prospecto_id
            $table->unsignedBigInteger('prospecto_id')->nullable()->change();
            
            // Volver a agregar la restricción de clave foránea
            $table->foreign('prospecto_id')->references('id')->on('prospectos')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proformas', function (Blueprint $table) {
            // Eliminar la restricción de clave foránea
            $table->dropForeign(['prospecto_id']);
            
            // Hacer NOT NULL el campo prospecto_id
            $table->unsignedBigInteger('prospecto_id')->nullable(false)->change();
            
            // Volver a agregar la restricción de clave foránea
            $table->foreign('prospecto_id')->references('id')->on('prospectos')->onDelete('restrict');
        });
    }
};
