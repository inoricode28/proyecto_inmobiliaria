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
    public function up(): void
    {
        Schema::create('separaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proforma_id')->constrained()->onDelete('cascade');

            $table->enum('tipo_separacion', ['Separación de Bienes', 'Con poderes', 'Divorciado', 'Ninguno'])->nullable();
            $table->string('numero_partida')->nullable();
            $table->string('lugar_partida')->nullable();
            $table->decimal('co_propietario_porcentaje', 5, 2)->nullable();

            $table->foreignId('ocupacion_id')->nullable()->constrained('ocupaciones');
            $table->foreignId('profesion_id')->nullable()->constrained('profesiones');
            $table->string('puesto')->nullable();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias');

            $table->string('ruc')->nullable();
            $table->string('empresa')->nullable();
            $table->string('pep')->nullable();
            $table->date('fecha_pep')->nullable();
            $table->string('direccion_laboral')->nullable();
            $table->string('urbanizacion')->nullable();

            $table->foreignId('departamento_ubigeo_id')->nullable()->constrained('departamentos_ubigeo');
            $table->foreignId('provincia_id')->nullable()->constrained('provincias');
            $table->foreignId('distrito_id')->nullable()->constrained('distritos');

            $table->string('telefono1')->nullable();
            $table->string('telefono2')->nullable();
            $table->string('antiguedad_laboral')->nullable();
            $table->decimal('ingresos', 12, 2)->nullable();
            $table->decimal('saldo_a_financiar', 12, 2)->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('separaciones');
    }
};
