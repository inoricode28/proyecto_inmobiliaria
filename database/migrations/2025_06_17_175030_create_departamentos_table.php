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
        Schema::create('departamentos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('edificio_id')->constrained('edificios');
                $table->string('numero', 20);
                $table->integer('piso');
                $table->decimal('area_total', 10, 2)->nullable();
                $table->decimal('area_construida', 10, 2)->nullable();
                $table->integer('numero_habitaciones')->nullable();
                $table->integer('numero_banos')->nullable();
                $table->boolean('tiene_balcon')->default(false);
                $table->foreignId('tipo_departamento_id')->nullable()->constrained('tipos_departamento');
                $table->foreignId('estado_departamento_id')->nullable()->constrained('estados_departamento');
                $table->decimal('precio', 12, 2)->nullable();
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
        Schema::dropIfExists('departamentos');
    }
};
