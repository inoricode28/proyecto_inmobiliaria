<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('tipos_cuota')) {
            Schema::create('tipos_cuota', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 100)->unique();
                $table->string('descripcion', 255)->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });

            // Insertar datos iniciales
            DB::table('tipos_cuota')->insert([
                ['nombre' => 'Cuota Inicial', 'descripcion' => 'Cuota inicial del inmueble', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['nombre' => 'Ahorro Casa', 'descripcion' => 'Ahorro para la casa', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['nombre' => 'AFP Titular', 'descripcion' => 'AFP del titular', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['nombre' => 'AFP Cónyuge', 'descripcion' => 'AFP del cónyuge', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipos_cuota');
    }
};
