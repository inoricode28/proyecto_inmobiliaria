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
        Schema::table('cronogramas_saldo_financiar', function (Blueprint $table) {
            // Cambiar el campo tipo_comprobante de string a foreign key
            $table->dropColumn('tipo_comprobante');
            $table->foreignId('tipo_comprobante_id')->nullable()->constrained('tipos_comprobante')->after('banco_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cronogramas_saldo_financiar', function (Blueprint $table) {
            // Revertir los cambios
            $table->dropForeign(['tipo_comprobante_id']);
            $table->dropColumn('tipo_comprobante_id');
            $table->string('tipo_comprobante')->nullable()->after('banco_id');
        });
    }
};
