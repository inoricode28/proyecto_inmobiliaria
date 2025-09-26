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
        if (!Schema::hasTable('cronograma_saldo_financiar_detalles')) {
            Schema::create('cronograma_saldo_financiar_detalles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cronograma_sf_id');
                $table->foreign('cronograma_sf_id', 'cronograma_sf_detalles_foreign')
                      ->references('id')
                      ->on('cronogramas_saldo_financiar')
                      ->onDelete('cascade');
                
                // Información de la cuota
                $table->integer('numero_cuota');
                $table->date('fecha_pago');
                $table->decimal('monto', 10, 2);
                $table->string('motivo'); // Saldo a financiar, etc.
                $table->enum('estado', ['Pendiente', 'Pagado', 'Vencido', 'Cancelado'])->default('Pendiente');
                
                // Información adicional
                $table->text('observaciones')->nullable();
                
                $table->timestamps();
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
        Schema::dropIfExists('cronograma_saldo_financiar_detalles');
    }
};
