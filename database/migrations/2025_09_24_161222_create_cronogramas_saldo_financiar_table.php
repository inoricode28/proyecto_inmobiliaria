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
        if (!Schema::hasTable('cronogramas_saldo_financiar')) {
            Schema::create('cronogramas_saldo_financiar', function (Blueprint $table) {
                $table->id();
                $table->foreignId('separacion_id')->constrained('separaciones')->onDelete('cascade');
                
                // Información del cronograma
                $table->date('fecha_inicio');
                $table->decimal('monto_total', 12, 2);
                $table->decimal('saldo_financiar', 12, 2);
                $table->integer('numero_cuotas');
                
                // Información de financiamiento
                $table->foreignId('tipo_financiamiento_id')->constrained('tipos_financiamiento');
                $table->foreignId('banco_id')->nullable()->constrained('bancos');
                $table->string('tipo_comprobante')->nullable(); // BOLETA, FACTURA, etc.
                
                // Bonos
                $table->boolean('bono_mivivienda')->default(false);
                $table->boolean('bono_verde')->default(false);
                $table->boolean('bono_integrador')->default(false);
                
                // Campos de auditoría
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
        Schema::dropIfExists('cronogramas_saldo_financiar');
    }
};
