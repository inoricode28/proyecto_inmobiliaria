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
        Schema::table('separaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('banco_id')->nullable()->after('id');
        });
    }

    public function down()
    {
        Schema::table('separaciones', function (Blueprint $table) {
            $table->dropColumn('banco_id');
        });
    }

};
