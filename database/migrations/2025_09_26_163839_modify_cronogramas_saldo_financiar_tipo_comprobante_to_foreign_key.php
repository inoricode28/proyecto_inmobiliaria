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
            // Verificar si la columna tipo_comprobante existe antes de eliminarla
            if (Schema::hasColumn('cronogramas_saldo_financiar', 'tipo_comprobante')) {
                $table->dropColumn('tipo_comprobante');
            }
            
            // Verificar si la columna tipo_comprobante_id no existe antes de agregarla
            if (!Schema::hasColumn('cronogramas_saldo_financiar', 'tipo_comprobante_id')) {
                $table->foreignId('tipo_comprobante_id')->nullable()->constrained('tipos_comprobante')->after('banco_id');
            }
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
