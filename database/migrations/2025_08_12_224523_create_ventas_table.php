<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            
            // Llave foránea a separaciones
            $table->unsignedBigInteger('separacion_id');
            $table->foreign('separacion_id')
                  ->references('id')
                  ->on('separaciones')
                  ->onDelete('restrict'); // o 'cascade' según tu necesidad
            
            // Campos de fechas
            $table->date('fecha_entrega_inicial')->nullable();
            $table->date('fecha_venta')->nullable();
            $table->date('fecha_preminuta')->nullable();
            $table->date('fecha_minuta')->nullable();
            
            // Campo de estado
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            
            // Campos de auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index('separacion_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('ventas');
    }
};