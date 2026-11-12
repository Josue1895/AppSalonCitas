<?php

namespace Tests\Unit;

use App\Models\Horario;
use PHPUnit\Framework\TestCase;

class HorarioTest extends TestCase
{
    public function test_la_semana_empieza_en_lunes_y_termina_en_domingo(): void
    {
        // Preparar y actuar: tomar la lista de días del modelo
        $dias = Horario::DIAS;

        // Comprobar
        $this->assertCount(7, $dias);
        $this->assertSame('Lunes', $dias[1]);
        $this->assertSame('Domingo', $dias[7]);
    }
}
