<?php

namespace Tests\Feature\Requerimientos\NoFuncionales;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Group;
use Tests\Feature\Requerimientos\RequerimientoTestCase;

/**
 * RNF-04 Mantenibilidad: se cumple con la arquitectura MVC en Laravel y con
 * la base de datos.
 *
 * Se revisa la estructura del código sin modificarlo: rutas que apuntan a
 * controladores, modelos Eloquent con sus tablas y relaciones, vistas Blade
 * que no consultan la base de datos y validación separada en Form Requests.
 */
#[Group('RNF-04')]
class RNF04MantenibilidadTest extends RequerimientoTestCase
{
    public function test_las_rutas_de_la_app_apuntan_a_controladores(): void
    {
        $sinControlador = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($r) => ! str_starts_with($r->uri(), '_') && ! str_starts_with($r->uri(), 'storage') && $r->uri() !== 'up')
            ->filter(fn ($r) => $r->getActionName() === 'Closure')
            ->map(fn ($r) => $r->uri())
            ->values()
            ->all();

        $this->assertSame([], $sinControlador, 'Hay rutas con la lógica escrita directamente en web.php: ' . implode(', ', $sinControlador));
    }

    public function test_todos_los_controladores_extienden_la_clase_base(): void
    {
        foreach (File::files(app_path('Http/Controllers')) as $archivo) {
            $clase = 'App\\Http\\Controllers\\' . $archivo->getFilenameWithoutExtension();

            if ($clase === Controller::class) {
                continue;
            }

            $this->assertTrue(is_subclass_of($clase, Controller::class), "{$clase} no extiende Controller.");
        }
    }

    public function test_los_modelos_son_eloquent_y_usan_las_tablas_del_diagrama(): void
    {
        $esperado = [
            \App\Models\User::class => 'usuarios',
            \App\Models\Service::class => 'servicios',
            \App\Models\Appointment::class => 'citas',
            \App\Models\AppointmentService::class => 'citas_servicios',
            \App\Models\Horario::class => 'horarios',
        ];

        foreach ($esperado as $clase => $tabla) {
            $modelo = new $clase();
            $this->assertInstanceOf(Model::class, $modelo);
            $this->assertSame($tabla, $modelo->getTable(), "{$clase} no usa la tabla {$tabla}.");
        }
    }

    public function test_las_relaciones_entre_modelos_funcionan(): void
    {
        $cliente = $this->crearCliente();
        $servicio = $this->crearServicio(['nombre' => 'Pedicure']);
        $cita = $this->crearCita($cliente, [], [$servicio]);

        $cita->load('citasServicios.servicio');

        $this->assertCount(1, $cita->citasServicios);
        $this->assertSame('Pedicure', $cita->citasServicios->first()->servicio->nombre);
    }

    public function test_las_vistas_no_consultan_la_base_de_datos(): void
    {
        $conConsultas = collect(File::allFiles(resource_path('views')))
            ->filter(fn ($f) => preg_match('/DB::|::(where|query|all|find)\(/', $f->getContents()))
            ->map(fn ($f) => $f->getRelativePathname())
            ->values()
            ->all();

        $this->assertSame([], $conConsultas, 'Estas vistas hacen consultas a la base de datos: ' . implode(', ', $conConsultas));
    }

    public function test_la_validacion_esta_separada_en_form_requests(): void
    {
        foreach (['AppointmentRequest', 'LoginRequest', 'RegisterRequest'] as $nombre) {
            $clase = "App\\Http\\Requests\\{$nombre}";
            $this->assertTrue(class_exists($clase), "Falta {$clase}.");
            $this->assertTrue(is_subclass_of($clase, FormRequest::class), "{$clase} no es un FormRequest.");
        }
    }

    public function test_la_base_de_datos_se_versiona_con_migraciones(): void
    {
        $migraciones = collect(File::files(database_path('migrations')))->map->getFilename()->implode(' ');

        foreach (['usuarios', 'servicios', 'citas', 'citas_servicios', 'horarios'] as $tabla) {
            $this->assertMatchesRegularExpression("/create_{$tabla}_table/", $migraciones, "No hay migración para la tabla {$tabla}.");
        }
    }

    public function test_el_panel_de_administracion_se_protege_con_un_middleware(): void
    {
        $sinMiddleware = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($r) => str_starts_with($r->uri(), 'admin'))
            ->reject(fn ($r) => in_array('admin', $r->gatherMiddleware(), true))
            ->map(fn ($r) => $r->uri())
            ->values()
            ->all();

        $this->assertSame([], $sinMiddleware);
    }
}
