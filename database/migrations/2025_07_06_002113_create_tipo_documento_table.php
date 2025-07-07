<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('tipo_documento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();  
            $table->text('descripcion')->nullable(); 
            $table->softDeletes();                  
            $table->timestamps();                 
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipo_documento');
    }
};