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
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyecto_id')->nullable()->constrained('proyectos');
            $table->string('centro_costos', 100)->nullable();
            $table->foreignId('edificio_id')->constrained('edificios');
            
            // Corrected table names to use Laravel's naming conventions (plural)
            $table->foreignId('tipo_inmueble_id')->nullable()->constrained('tipo_inmueble');
            $table->foreignId('tipo_departamento_id')->nullable()->constrained('tipos_departamento');
            $table->foreignId('estado_departamento_id')->nullable()->constrained('estados_departamento');
            
            $table->string('numero_inicial', 20)->nullable();
            $table->string('numero_final', 20)->nullable();
            $table->string('ficha_indep', 100)->nullable();
            $table->string('num_departamento', 255)->nullable(); // Nuevo campo agregado
            $table->unsignedSmallInteger('num_piso')->nullable();
            $table->unsignedTinyInteger('num_dormitorios')->nullable();
            $table->unsignedTinyInteger('num_bano')->nullable();
            $table->string('num_certificado', 50)->nullable();
            $table->boolean('bono_techo_propio')->default(false);
            $table->string('num_bono_tp', 50)->nullable();
            $table->decimal('cantidad_uit', 10, 2)->nullable();
            $table->string('codigo_bancario', 50)->nullable();
            $table->string('codigo_catastral', 50)->nullable();
            $table->foreignId('vista_id')->nullable()->constrained('vistas');
            $table->unsignedSmallInteger('orden')->nullable();
            
            // Changed from modena_id to moneda_id for consistency
            $table->foreignId('moneda_id')->nullable()->constrained('moneda');
            
            // Changed price fields to decimal for better precision
            $table->decimal('precio', 12, 2)->nullable();
            $table->decimal('Precio_lista', 12, 2)->nullable();
            $table->decimal('Precio_venta', 12, 2)->nullable();
            
            // Changed from descuent to descuento and made it decimal
            $table->decimal('descuento', 5, 2)->nullable()->comment('Porcentaje de descuento');
            
            $table->decimal('predio_m2', 10, 2)->nullable();
            $table->decimal('terreno', 10, 2)->nullable();
            $table->decimal('techada', 10, 2)->nullable();
            $table->decimal('construida', 10, 2)->nullable();
            $table->decimal('terraza', 10, 2)->nullable();
            $table->decimal('jardin', 10, 2)->nullable();
            $table->string('adicional', 100)->nullable();
            $table->boolean('vendible')->default(true);
            $table->decimal('frente', 10, 2)->nullable();
            $table->decimal('derecha', 10, 2)->nullable();
            $table->decimal('izquierda', 10, 2)->nullable();
            $table->decimal('fondo', 10, 2)->nullable();
            $table->text('direccion')->nullable();
            $table->text('observaciones')->nullable();
            
            // Corrected table name to plural
            $table->foreignId('estado_id')->default(1)->constrained('estado');
            
            $table->timestamps();
            $table->softDeletes(); // Added soft delete capability
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('departamentos');
    }
};