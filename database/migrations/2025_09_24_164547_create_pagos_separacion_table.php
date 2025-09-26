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
        if (!Schema::hasTable('pagos_separacion')) {
            Schema::create('pagos_separacion', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('separacion_id');
                $table->date('fecha_pago');
                $table->decimal('monto', 12, 2);
                $table->decimal('tipo_cambio', 8, 4)->default(1.0000);
                $table->decimal('monto_pago', 12, 2);
                $table->unsignedBigInteger('moneda_id');
                $table->unsignedBigInteger('medio_pago_id');
                $table->unsignedBigInteger('cuenta_bancaria_id')->nullable();
                $table->string('numero_operacion', 100)->nullable();
                $table->string('numero_documento', 100)->nullable();
                $table->string('agencia_bancaria', 200)->nullable();
                $table->string('archivo_comprobante', 500)->nullable();
                $table->text('observaciones')->nullable();
                $table->unsignedBigInteger('registrado_por');
                $table->timestamps();

                // Foreign keys
                $table->foreign('separacion_id')->references('id')->on('separaciones')->onDelete('cascade');
                $table->foreign('moneda_id')->references('id')->on('moneda');
                $table->foreign('medio_pago_id')->references('id')->on('medios_pago');
                $table->foreign('cuenta_bancaria_id')->references('id')->on('cuentas_bancarias')->onDelete('set null');
                $table->foreign('registrado_por')->references('id')->on('users');

                // Indexes
                $table->index(['separacion_id', 'fecha_pago']);
                $table->index('numero_operacion');
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
        Schema::dropIfExists('pagos_separacion');
    }
};
