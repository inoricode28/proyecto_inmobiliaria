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
        Schema::create('notaria_kardex', function (Blueprint $table) {
            $table->id();
            $table->foreignId('separacion_id')->constrained('separaciones')->onDelete('cascade');

            $table->string('notaria')->nullable();
            $table->string('responsable')->nullable();
            $table->string('direccion')->nullable();
            $table->string('email')->nullable();
            $table->string('celular')->nullable();
            $table->string('telefono')->nullable();
            $table->string('numero_kardex')->nullable();
            $table->string('oficina')->nullable();
            $table->string('numero_registro')->nullable();
            $table->string('agencia')->nullable();
            $table->string('asesor')->nullable();
            $table->string('telefonos')->nullable();
            $table->string('correos')->nullable();
            $table->date('fecha_vencimiento_carta')->nullable();
            $table->date('fecha_escritura_publica')->nullable();
            $table->string('penalidad_entrega')->nullable();

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
        Schema::dropIfExists('notaria_kardex');
    }
};
