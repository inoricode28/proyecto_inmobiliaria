<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Empresa;
use App\Models\EstadoProyecto;
use App\Models\Proyecto;
use App\Models\Edificio;
use App\Models\EstadoDepartamento;
use App\Models\Departamento;
use App\Models\Separacion;

class SeparacionesMultipleTest extends TestCase
{
    use RefreshDatabase;

    public function test_crea_dos_separaciones_en_multiple(): void
    {
        // Autenticar usuario verificado
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $this->actingAs($user);

        // Asegurar existencia de estado base (id=1) para FK en departamentos
        DB::table('estado')->insert(['id' => 1, 'activo' => true]);

        // Crear estados de departamento: Disponible y Separacion
        $estadoDisponible = EstadoDepartamento::create([
            'nombre' => 'Disponible',
            'descripcion' => 'Disponible para venta',
            'is_default' => true,
        ]);
        $estadoSeparacion = EstadoDepartamento::create([
            'nombre' => 'Separacion',
            'descripcion' => 'Con separación activa',
            'is_default' => false,
        ]);

        // Datos base: empresa, estado de proyecto, proyecto y edificio
        $empresa = Empresa::create([
            'nombre' => 'Constructora Demo',
            'ruc' => '20123456789',
            'direccion' => 'Av. Principal 123',
            'telefono' => '999888777',
            'email' => 'contacto@demo.com',
            'representante_legal' => 'Juan Perez',
        ]);

        $estadoProyecto = EstadoProyecto::create([
            'nombre' => 'En ejecución',
            'descripcion' => 'Proyecto en curso',
        ]);

        $proyecto = Proyecto::create([
            'nombre' => 'Proyecto Sol',
            'descripcion' => 'Residencial Sol',
            'ubicacion' => 'Lima',
            'fecha_inicio' => now()->subMonths(3),
            'fecha_entrega' => now()->addMonths(9),
            'estado_proyecto_id' => $estadoProyecto->id,
            'empresa_constructora_id' => $empresa->id,
        ]);

        $edificio = Edificio::create([
            'proyecto_id' => $proyecto->id,
            'nombre' => 'Torre A',
            'descripcion' => 'Primera torre',
            'cantidad_pisos' => 10,
            'cantidad_departamentos' => 40,
            'fecha_inicio' => now()->subMonths(2),
            'fecha_entrega' => now()->addMonths(10),
        ]);

        // Crear departamentos SS03 y SS04
        $dep1 = DB::table('departamentos')->insertGetId([
            'proyecto_id' => $proyecto->id,
            'edificio_id' => $edificio->id,
            'estado_departamento_id' => $estadoDisponible->id,
            'num_departamento' => 'SS03',
            'num_piso' => 3,
            'num_dormitorios' => 2,
            'Precio_lista' => 200000,
            'Precio_venta' => null,
            'descuento' => null,
            'vendible' => true,
            'estado_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dep2 = DB::table('departamentos')->insertGetId([
            'proyecto_id' => $proyecto->id,
            'edificio_id' => $edificio->id,
            'estado_departamento_id' => $estadoDisponible->id,
            'num_departamento' => 'SS04',
            'num_piso' => 4,
            'num_dormitorios' => 3,
            'Precio_lista' => 250000,
            'Precio_venta' => null,
            'descuento' => null,
            'vendible' => true,
            'estado_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Payload para múltiples separaciones
        $payload = [
            'propiedades' => [
                [
                    'departamento_id' => $dep1,
                    'precio_lista' => 200000,
                    'precio_venta' => 195000,
                    'monto_separacion' => 3000,
                    'monto_cuota_inicial' => 15000,
                    'saldo_financiar' => 180000,
                ],
                [
                    'departamento_id' => $dep2,
                    'precio_lista' => 250000,
                    'precio_venta' => 240000,
                    'monto_separacion' => 4000,
                    'monto_cuota_inicial' => 20000,
                    'saldo_financiar' => 216000,
                ],
            ],
            'cliente_data' => [
                'nombres' => 'Carlos',
                'ape_paterno' => 'Gomez',
                'ape_materno' => 'Lopez',
                'numero_documento' => '12345678',
                'email' => 'carlos@example.com',
                'telefono' => '987654321',
            ],
        ];

        // Ejecutar petición
        $response = $this->postJson('/separaciones/multiple', $payload);

        // Validaciones básicas de respuesta
        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('data.separaciones_creadas', 2)
                 ->assertJsonCount(2, 'data.separaciones');

        // Validar totales
        $response->assertJsonPath('data.totales.monto_separacion', 7000)
                 ->assertJsonPath('data.totales.monto_cuota_inicial', 35000)
                 ->assertJsonPath('data.totales.saldo_a_financiar', 396000);

        // Validar que separaciones fueron creadas en DB
        $this->assertEquals(2, Separacion::count());

        // Validar cambio de estado de departamentos a 'Separacion'
        $dep1Model = Departamento::find($dep1);
        $dep2Model = Departamento::find($dep2);
        $this->assertNotNull($dep1Model);
        $this->assertNotNull($dep2Model);
        $this->assertEquals($estadoSeparacion->id, $dep1Model->estado_departamento_id);
        $this->assertEquals($estadoSeparacion->id, $dep2Model->estado_departamento_id);
    }
}