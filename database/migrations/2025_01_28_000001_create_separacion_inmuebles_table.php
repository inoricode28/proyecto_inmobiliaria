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
        if (!Schema::hasTable('separacion_inmuebles')) {
            Schema::create('separacion_inmuebles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('separacion_id')->constrained('separaciones')->onDelete('cascade');
                $table->foreignId('departamento_id')->constrained('departamentos')->onDelete('restrict');
                
                // Campos específicos del inmueble en esta separación
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
                $table->index(['separacion_id', 'orden']);
                $table->unique(['separacion_id', 'departamento_id']); // Un inmueble no puede repetirse en la misma separación
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
        Schema::dropIfExists('separacion_inmuebles');
    }
};