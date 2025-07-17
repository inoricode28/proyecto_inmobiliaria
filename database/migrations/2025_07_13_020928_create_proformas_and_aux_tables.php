<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tablas auxiliares
        Schema::create('generos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::create('estados_civiles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::create('grados_estudio', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::create('nacionalidades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::create('departamentos_ubigeo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::create('provincias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('departamento_ubigeo_id')->constrained('departamentos_ubigeo')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('distritos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('provincia_id')->constrained('provincias')->onDelete('cascade');
            $table->timestamps();
        });

        // Tabla principal: PROFORMAS
        Schema::create('proformas', function (Blueprint $table) {
            $table->id();

            // Relaciones base
            $table->foreignId('prospecto_id')->constrained('prospectos')->onDelete('cascade');
            $table->foreignId('proyecto_id')->constrained('proyectos')->onDelete('restrict');
            $table->foreignId('departamento_id')->constrained('departamentos')->onDelete('restrict'); // inmueble

            // Datos personales
            $table->foreignId('tipo_documento_id')->nullable()->constrained('tipo_documento');
            $table->string('numero_documento')->nullable();
            $table->string('nombres')->nullable();
            $table->string('ape_paterno')->nullable();
            $table->string('ape_materno')->nullable();
            $table->string('razon_social')->nullable();
            $table->foreignId('genero_id')->nullable()->constrained('generos');
            $table->date('fecha_nacimiento')->nullable();
            $table->foreignId('nacionalidad_id')->nullable()->constrained('nacionalidades');
            $table->foreignId('estado_civil_id')->nullable()->constrained('estados_civiles');
            $table->foreignId('grado_estudio_id')->nullable()->constrained('grados_estudio');

            // Contacto
            $table->string('telefono_casa')->nullable();
            $table->string('celular')->nullable();
            $table->string('email')->nullable();

            // Dirección
            $table->string('direccion')->nullable();
            $table->foreignId('departamento_ubigeo_id')->nullable()->constrained('departamentos_ubigeo');
            $table->foreignId('provincia_id')->nullable()->constrained('provincias');
            $table->foreignId('distrito_id')->nullable()->constrained('distritos');
            $table->string('direccion_adicional')->nullable();

            // Inmueble (comercial)
            $table->decimal('monto_separacion', 10, 2)->nullable();
            $table->decimal('monto_cuota_inicial', 10, 2)->nullable();

            // Observaciones
            $table->text('observaciones')->nullable();

            // Auditoría
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proformas');
        Schema::dropIfExists('distritos');
        Schema::dropIfExists('provincias');
        Schema::dropIfExists('departamentos_ubigeo');
        Schema::dropIfExists('nacionalidades');
        Schema::dropIfExists('grados_estudio');
        Schema::dropIfExists('estados_civiles');
        Schema::dropIfExists('generos');
    }
};
