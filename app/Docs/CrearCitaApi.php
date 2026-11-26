<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Post(
    path: "/api/citas",
    summary: "Crear cita",
    tags: ["Citas"]
)]
#[OA\RequestBody(
    required: true,
    content: new OA\JsonContent(
        required: ["fecha", "hora", "usuarioId"],
        properties: [
            new OA\Property(property: "fecha", type: "string", example: "2026-06-01"),
            new OA\Property(property: "hora", type: "string", example: "10:00"),
            new OA\Property(property: "usuarioId", type: "integer", example: 2)
        ]
    )
)]
#[OA\Response(
    response: 200,
    description: "Cita creada correctamente"
)]
class CrearCitaApi
{
}