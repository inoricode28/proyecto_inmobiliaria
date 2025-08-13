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
        Schema::table('tareas', function (Blueprint $table) {
            $table->unsignedBigInteger('tarea_padre_id')->nullable()->after('prospecto_id');

            $table->foreign('tarea_padre_id')
                  ->references('id')->on('tareas')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::table('tareas', function (Blueprint $table) {
            $table->dropForeign(['tarea_padre_id']);
            $table->dropColumn('tarea_padre_id');
        });
    }
};
