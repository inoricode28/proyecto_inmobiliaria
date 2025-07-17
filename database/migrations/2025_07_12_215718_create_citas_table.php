<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tarea_id');
            $table->unsignedBigInteger('proyecto_id');
            $table->unsignedBigInteger('responsable_id');

            $table->date('fecha_cita');
            $table->time('hora_cita');

            $table->enum('modalidad', ['presencial', 'virtual'])->default('presencial');
            $table->string('lugar')->nullable();
            $table->text('comentarios')->nullable();

            // Auditoría
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('tarea_id')->references('id')->on('tareas')->onDelete('cascade');
            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('restrict');
            $table->foreign('responsable_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
}
