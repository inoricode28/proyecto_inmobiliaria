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
        Schema::table('cronograma_cuota_inicial', function (Blueprint $table) {
            // Verificar si las columnas no existen antes de agregarlas
            if (!Schema::hasColumn('cronograma_cuota_inicial', 'tipo_cuota_id')) {
                $table->unsignedBigInteger('tipo_cuota_id')->nullable()->after('monto');
                // Agregar la foreign key solo si la columna fue creada
                $table->foreign('tipo_cuota_id')->references('id')->on('tipos_cuota')->onDelete('set null');
            }
            if (!Schema::hasColumn('cronograma_cuota_inicial', 'estado_id')) {
                $table->unsignedBigInteger('estado_id')->nullable()->after('tipo_cuota_id');
                // Agregar la foreign key solo si la columna fue creada
                $table->foreign('estado_id')->references('id')->on('estados_cuota')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cronograma_cuota_inicial', function (Blueprint $table) {
            // Eliminar las claves foráneas
            $table->dropForeign(['tipo_cuota_id']);
            $table->dropForeign(['estado_id']);
            
            // Eliminar las columnas
            $table->dropColumn(['tipo_cuota_id', 'estado_id']);
            
            // Restaurar las columnas antiguas si es necesario
            // $table->string('tipo')->nullable();
            // $table->string('estado')->nullable();
        });
    }
};
