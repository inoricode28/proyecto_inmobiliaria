<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechaVencimientoToSeparacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('separaciones', function (Blueprint $table) {
            $table->date('fecha_vencimiento')
                  ->nullable()
                  ->after('saldo_a_financiar'); // Puedes cambiar la posición según necesites
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('separaciones', function (Blueprint $table) {
            $table->dropColumn('fecha_vencimiento');
        });
    }
}