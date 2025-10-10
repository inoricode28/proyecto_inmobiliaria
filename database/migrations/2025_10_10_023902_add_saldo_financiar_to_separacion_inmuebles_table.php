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
        Schema::table('separacion_inmuebles', function (Blueprint $table) {
            if (!Schema::hasColumn('separacion_inmuebles', 'saldo_financiar')) {
                $table->decimal('saldo_financiar', 12, 2)->nullable()->after('monto_cuota_inicial');
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
        Schema::table('separacion_inmuebles', function (Blueprint $table) {
            if (Schema::hasColumn('separacion_inmuebles', 'saldo_financiar')) {
                $table->dropColumn('saldo_financiar');
            }
        });
    }
};
