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
        if (!Schema::hasTable('estados_cuota')) {
            Schema::create('estados_cuota', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 100)->unique();
                $table->string('descripcion', 255)->nullable();
                $table->string('color', 20)->nullable(); // Para mostrar colores en la UI
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });

            // Insertar datos iniciales
            DB::table('estados_cuota')->insert([
                ['nombre' => 'Pendiente', 'descripcion' => 'Cuota pendiente de pago', 'color' => 'yellow', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['nombre' => 'Pagado', 'descripcion' => 'Cuota pagada', 'color' => 'green', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['nombre' => 'Vencido', 'descripcion' => 'Cuota vencida', 'color' => 'red', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['nombre' => 'Cancelado', 'descripcion' => 'Cuota cancelada', 'color' => 'gray', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
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
        Schema::dropIfExists('estados_cuota');
    }
};
