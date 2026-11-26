<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AppointmentModifyButtonTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('citas');
        Schema::create('citas', function (Blueprint $table): void {
            $table->id();
            $table->date('fecha');
            $table->time('hora');
            $table->unsignedBigInteger('usuarioId');
            $table->decimal('total', 8, 2)->nullable();
            $table->string('estado');
        });
    }

    public function test_pending_appointment_uses_the_modify_button_label(): void
    {
        $cliente = new User(['admin' => false]);
        $cliente->id = 1;

        DB::table('citas')->insert([
            'fecha' => '2026-12-01',
            'hora' => '10:00:00',
            'usuarioId' => $cliente->id,
            'total' => 25000,
            'estado' => 'pendiente',
        ]);

        $response = $this->actingAs($cliente)->get(route('citas.mis'));

        $response->assertOk()
            ->assertSee('Modificar')
            ->assertDontSee('Editar');
    }
}
