<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Get(
    path: "/api/citas/usuario/{id}",
    summary: "Obtener citas por usuario",
    tags: ["Citas"]
)]
#[OA\Parameter(
    name: "id",
    in: "path",
    required: true,
    schema: new OA\Schema(type: "integer")
)]
#[OA\Response(
    response: 200,
    description: "Listado de citas"
)]
class CitasUsuarioApi
{
}