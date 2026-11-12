<?php

namespace Tests\Feature\Requerimientos\Administrador;

use App\Exports\CitasExport;
use Maatwebsite\Excel\Facades\Excel;
use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RF-16 / 17 / 18 Reportes: menú Reportes. Se filtra por rango de fechas, se
 * ven los ingresos totales y las citas, y hay botones para descargar en PDF o
 * en Excel.
 *
 * Datos base:
 *  - 05/10 completada  $50.000
 *  - 10/10 completada  $30.000
 *  - 12/10 cancelada   $20.000
 *  - 05/11 completada $100.000  (fuera de octubre)
 */
#[Group('RF-16')]
#[Group('RF-17')]
#[Group('RF-18')]
class RF16a18ReportesTest extends RequerimientoTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $cliente = $this->crearCliente();

        $this->crearCita($cliente, ['fecha' => '2026-10-05', 'total' => 50000, 'estado' => 'completada']);
        $this->crearCita($cliente, ['fecha' => '2026-10-10', 'total' => 30000, 'estado' => 'completada']);
        $this->crearCita($cliente, ['fecha' => '2026-10-12', 'total' => 20000, 'estado' => 'cancelada']);
        $this->crearCita($cliente, ['fecha' => '2026-11-05', 'total' => 100000, 'estado' => 'completada']);
    }

    private const OCTUBRE = ['fecha_inicio' => '2026-10-01', 'fecha_fin' => '2026-10-31'];

    public function test_sin_filtro_muestra_todas_las_citas_e_ingresos(): void
    {
        $this->actingAs($this->crearAdmin())
            ->get(route('admin.reportes'))
            ->assertOk()
            ->assertViewHas('totalCitas', 4)
            ->assertViewHas('ingresos', fn ($ingresos) => (float) $ingresos === 180000.0);
    }

    public function test_filtrar_por_rango_de_fechas_muestra_solo_las_citas_del_rango(): void
    {
        $this->actingAs($this->crearAdmin())
            ->get(route('admin.reportes', self::OCTUBRE))
            ->assertOk()
            ->assertViewHas('totalCitas', 3)
            ->assertViewHas('citasCompletadas', 2)
            ->assertViewHas('citasCanceladas', 1)
            ->assertSee('80.000');
    }

    public function test_los_ingresos_solo_cuentan_citas_completadas(): void
    {
        $this->actingAs($this->crearAdmin())
            ->get(route('admin.reportes', self::OCTUBRE))
            ->assertViewHas('ingresos', fn ($ingresos) => (float) $ingresos === 80000.0);
    }

    public function test_una_fecha_final_anterior_a_la_inicial_es_rechazada(): void
    {
        $this->actingAs($this->crearAdmin())
            ->from(route('admin.reportes'))
            ->get(route('admin.reportes', ['fecha_inicio' => '2026-10-31', 'fecha_fin' => '2026-10-01']))
            ->assertSessionHasErrors('fecha_fin');
    }

    public function test_la_pantalla_tiene_botones_de_descarga_pdf_y_excel(): void
    {
        $this->actingAs($this->crearAdmin())
            ->get(route('admin.reportes'))
            ->assertSee(route('admin.reportes.pdf'), false)
            ->assertSee(route('admin.reportes.excel'), false);
    }

    public function test_descarga_el_reporte_en_pdf(): void
    {
        $respuesta = $this->actingAs($this->crearAdmin())
            ->get(route('admin.reportes.pdf', self::OCTUBRE));

        $respuesta->assertOk();
        $respuesta->assertHeader('content-type', 'application/pdf');
        $respuesta->assertDownload('reporte-citas.pdf');
        $this->assertStringStartsWith('%PDF', $respuesta->getContent());
    }

    public function test_descarga_el_reporte_en_excel_con_los_datos_filtrados(): void
    {
        Excel::fake();

        $this->actingAs($this->crearAdmin())
            ->get(route('admin.reportes.excel', self::OCTUBRE))
            ->assertOk();

        Excel::assertDownloaded('reporte-citas.xlsx', function (CitasExport $export) {
            $filas = collect($export->array());

            $totalCitas = $filas->firstWhere(0, 'Total de citas:')[1] ?? null;
            $ingresos = $filas->firstWhere(0, 'Ingresos realizados:')[1] ?? null;

            return $totalCitas === 3 && (float) $ingresos === 80000.0;
        });
    }

    public function test_un_cliente_no_puede_ver_ni_descargar_reportes(): void
    {
        $cliente = $this->crearCliente();

        foreach (['admin.reportes', 'admin.reportes.pdf', 'admin.reportes.excel'] as $ruta) {
            $this->actingAs($cliente)->get(route($ruta))->assertRedirect(route('home'));
        }
    }
}
