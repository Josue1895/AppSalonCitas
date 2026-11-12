<?php

namespace Tests\Feature\Requerimientos\Cliente;

use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RF-03 Servicios: está en Servicios y en la página de inicio. Cada tarjeta
 * muestra la descripción, el precio y la duración.
 */
#[Group('RF-03')]
class RF03ServiciosTest extends RequerimientoTestCase
{
    public function test_la_pagina_servicios_muestra_descripcion_precio_y_duracion(): void
    {
        $this->crearServicio([
            'nombre' => 'Manicure Spa',
            'descripcion' => 'Limpieza e hidratación de uñas',
            'precio' => 18000,
            'duracion' => 45,
        ]);

        $this->get(route('servicios.index'))
            ->assertOk()
            ->assertSee('Manicure Spa')
            ->assertSee('Limpieza e hidratación de uñas')
            ->assertSee('18000')
            ->assertSee('45 minutos');
    }

    public function test_la_pagina_servicios_no_muestra_servicios_inactivos(): void
    {
        $this->crearServicio(['nombre' => 'Servicio Activo']);
        $this->crearServicio(['nombre' => 'Servicio Retirado', 'activo' => false]);

        $this->get(route('servicios.index'))
            ->assertSee('Servicio Activo')
            ->assertDontSee('Servicio Retirado');
    }

    public function test_la_pagina_de_inicio_muestra_los_servicios_registrados(): void
    {
        $this->crearServicio([
            'nombre' => 'Keratina Brasileña',
            'descripcion' => 'Alisado con keratina',
            'precio' => 120000,
            'duracion' => 150,
        ]);

        $respuesta = $this->get(route('home'));

        $respuesta->assertOk();
        $respuesta->assertSee('Keratina Brasileña');
        $respuesta->assertSee('Alisado con keratina');
        $respuesta->assertSee('120000');
        $respuesta->assertSee('150');
    }
}
