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
    public function up(): void
    {
        Schema::create('cartas_fianza', function (Blueprint $table) {
            $table->id();
            $table->foreignId('separacion_id')->constrained('separaciones')->onDelete('cascade');
            $table->foreignId('banco_id')->nullable()->constrained('bancos');

            $table->decimal('monto', 12, 2)->nullable();
            $table->string('numero_carta')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cartas_fianza');
    }
};
