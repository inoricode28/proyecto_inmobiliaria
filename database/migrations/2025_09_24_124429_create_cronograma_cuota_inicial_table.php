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
        if (!Schema::hasTable('cronograma_cuota_inicial')) {
            Schema::create('cronograma_cuota_inicial', function (Blueprint $table) {
                $table->id();
                $table->foreignId('separacion_id')->constrained('separaciones')->onDelete('cascade');
                $table->date('fecha_pago');
                $table->decimal('monto', 10, 2);
                $table->string('tipo'); // Cuota Inicial, Ahorro Casa, AFP Titular, AFP Cónyuge
                $table->enum('estado', ['Pendiente', 'Pagado', 'Vencido', 'Cancelado'])->default('Pendiente');
                $table->text('observaciones')->nullable();
                $table->foreignId('created_by')->constrained('users');
                $table->foreignId('updated_by')->constrained('users');
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
        Schema::dropIfExists('cronograma_cuota_inicial');
    }
};
