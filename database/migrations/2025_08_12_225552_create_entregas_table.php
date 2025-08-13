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
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            
            // Relación con ventas
            $table->unsignedBigInteger('venta_id');
            $table->foreign('venta_id')
                  ->references('id')
                  ->on('ventas')
                  ->onDelete('cascade'); // Elimina entrega si se elimina la venta
            
            // Relación con prospectos
            $table->unsignedBigInteger('prospecto_id');
            $table->foreign('prospecto_id')
                  ->references('id')
                  ->on('prospectos')
                  ->onDelete('restrict');
            
            // Relación con departamentos (inmuebles)
            $table->unsignedBigInteger('departamento_id');
            $table->foreign('departamento_id')
                  ->references('id')
                  ->on('departamentos')
                  ->onDelete('restrict');
            
            // Campos de fechas
            $table->date('fecha_entrega')->nullable();
            $table->date('fecha_garantia_acabados')->nullable();
            $table->date('fecha_garantia_vicios_ocultos')->nullable();
            
            // Descripción
            $table->text('descripcion')->nullable();
            
            // Campos de auditoría
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index('venta_id');
            $table->index('prospecto_id');
            $table->index('departamento_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('entregas');
    }
};