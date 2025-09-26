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
        Schema::create('tipos_comprobante', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique(); // BOLETA, FACTURA, etc.
            $table->string('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
        
        // Insertar tipos de comprobante por defecto
        DB::table('tipos_comprobante')->insert([
            [
                'nombre' => 'BOLETA',
                'descripcion' => 'Boleta de Venta',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'FACTURA',
                'descripcion' => 'Factura de Venta',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipos_comprobante');
    }
};
