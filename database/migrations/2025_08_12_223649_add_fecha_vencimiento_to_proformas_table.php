<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechaVencimientoToProformasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proformas', function (Blueprint $table) {
            $table->date('fecha_vencimiento')->nullable()->after('monto_cuota_inicial');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proformas', function (Blueprint $table) {
            $table->dropColumn('fecha_vencimiento');
        });
    }
}