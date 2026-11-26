<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Put(
    path: "/api/citas/{id}",
    summary: "Actualizar cita",
    tags: ["Citas"]
)]
#[OA\Parameter(
    name: "id",
    in: "path",
    required: true,
    schema: new OA\Schema(type: "integer")
)]
#[OA\RequestBody(
    required: true,
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: "fecha", type: "string", example: "2026-06-02"),
            new OA\Property(property: "hora", type: "string", example: "15:00")
        ]
    )
)]
#[OA\Response(
    response: 200,
    description: "Cita actualizada correctamente"
)]
class ActualizarCitaApi
{
}