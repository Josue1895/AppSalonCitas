<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Get(
    path: "/api/servicios",
    summary: "Obtener servicios",
    description: "Obtiene todos los servicios activos",
    tags: ["Servicios"]
)]
#[OA\Response(
    response: 200,
    description: "Listado de servicios"
)]
class ServiciosApi
{
}