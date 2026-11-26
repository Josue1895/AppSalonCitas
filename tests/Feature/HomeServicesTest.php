<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class HomeServicesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('servicios');
        Schema::create('servicios', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre', 60);
            $table->decimal('precio', 8, 2);
            $table->text('descripcion')->nullable();
            $table->integer('duracion')->default(60);
            $table->boolean('activo')->default(true);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function test_homepage_displays_active_services_with_description_price_and_duration(): void
    {
        DB::table('servicios')->insert([
            'nombre' => 'Corte de cabello',
            'precio' => 25000,
            'descripcion' => 'Corte personalizado',
            'duracion' => 45,
            'activo' => true,
        ]);
        DB::table('servicios')->insert([
            'nombre' => 'Servicio inactivo',
            'precio' => 10000,
            'descripcion' => 'No debe mostrarse',
            'duracion' => 30,
            'activo' => false,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('Corte de cabello')
            ->assertSee('Corte personalizado')
            ->assertSee('$25000 COP')
            ->assertSee('45 minutos')
            ->assertDontSee('Servicio inactivo');
    }

    public function test_homepage_shows_an_empty_state_when_no_active_services_exist(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('Pronto encontrarás aquí nuestros servicios.');
    }
}
