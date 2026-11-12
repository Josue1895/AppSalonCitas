<?php

namespace Tests\Feature\Requerimientos\Cliente;

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RF-08 Modificar: botón Modificar en Mis Citas. Abre Modificar Cita con los
 * datos precargados, y al guardar sale el aviso de cita modificada.
 */
#[Group('RF-08')]
class RF08ModificarCitaTest extends RequerimientoTestCase
{
    public function test_mis_citas_muestra_el_boton_modificar(): void
    {
        $cliente = $this->crearCliente();
        $this->crearCita($cliente);

        $this->actingAs($cliente)
            ->get(route('citas.mis'))
            ->assertSee('Modificar');
    }

    public function test_existe_una_ruta_para_modificar_citas_del_cliente(): void
    {
        $rutasModificar = collect(Route::getRoutes()->getRoutes())
            ->filter(function ($ruta) {
                $uri = $ruta->uri();

                return str_contains($uri, 'cita')
                    && !str_starts_with($uri, 'admin')
                    && preg_match('/modificar|editar|edit|update|actualizar/i', $uri . ' ' . $ruta->getName());
            });

        $this->assertNotEmpty(
            $rutasModificar,
            'No existe ninguna ruta para que el cliente modifique una cita (pantalla "Modificar Cita").'
        );
    }
}
