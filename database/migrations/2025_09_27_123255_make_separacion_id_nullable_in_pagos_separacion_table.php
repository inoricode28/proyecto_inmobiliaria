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
            // Hacer separacion_id nullable
            $table->unsignedBigInteger('separacion_id')->nullable()->change();
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
            // Revertir separacion_id a no nullable
            $table->unsignedBigInteger('separacion_id')->nullable(false)->change();
        });
    }
};
