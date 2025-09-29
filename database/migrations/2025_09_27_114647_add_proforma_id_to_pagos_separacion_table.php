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
        Schema::table('pagos_separacion', function (Blueprint $table) {
            $table->unsignedBigInteger('proforma_id')->nullable()->after('separacion_id');
            $table->foreign('proforma_id')->references('id')->on('proformas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pagos_separacion', function (Blueprint $table) {
            $table->dropForeign(['proforma_id']);
            $table->dropColumn('proforma_id');
        });
    }
};
