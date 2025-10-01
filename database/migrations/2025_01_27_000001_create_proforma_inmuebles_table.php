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
        if (!Schema::hasTable('proforma_inmuebles')) {
            Schema::create('proforma_inmuebles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('proforma_id')->constrained('proformas')->onDelete('cascade');
                $table->foreignId('departamento_id')->constrained('departamentos')->onDelete('restrict');
                
                // Campos específicos del inmueble en esta proforma
                $table->decimal('precio_lista', 10, 2)->nullable();
                $table->decimal('precio_venta', 10, 2)->nullable();
                $table->decimal('descuento', 5, 2)->nullable()->comment('Porcentaje de descuento');
                $table->decimal('monto_separacion', 10, 2)->nullable();
                $table->decimal('monto_cuota_inicial', 10, 2)->nullable();
                
                // Orden para mantener la secuencia de inmuebles
                $table->integer('orden')->default(1);
                
                // Indicar si es el inmueble principal (para compatibilidad)
                $table->boolean('es_principal')->default(false);
                
                $table->timestamps();
                
                // Índices
                $table->index(['proforma_id', 'orden']);
                $table->unique(['proforma_id', 'departamento_id']); // Un inmueble no puede repetirse en la misma proforma
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proforma_inmuebles');
    }
};