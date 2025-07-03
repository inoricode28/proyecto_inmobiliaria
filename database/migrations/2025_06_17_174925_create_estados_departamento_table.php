<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('estados_departamento', function (Blueprint $table) {
            $table->string('color')->default('#6b7280')->after('descripcion');
            $table->boolean('is_default')->default(false)->after('color');
        });
    }

    public function down()
    {
        Schema::table('estados_departamento', function (Blueprint $table) {
            $table->dropColumn(['color', 'is_default']);
        });
    }
};
