<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Post(
    path: "/api/login",
    summary: "Iniciar sesión",
    description: "Autentica un usuario y genera un token Sanctum",
    tags: ["Autenticación"]
)]
#[OA\RequestBody(
    required: true,
    content: new OA\JsonContent(
        required: ["email", "password"],
        properties: [
            new OA\Property(
                property: "email",
                type: "string",
                example: "cliente@appsalon.com"
            ),
            new OA\Property(
                property: "password",
                type: "string",
                example: "123456"
            )
        ]
    )
)]
#[OA\Response(
    response: 200,
    description: "Login exitoso"
)]
#[OA\Response(
    response: 401,
    description: "Credenciales incorrectas"
)]
class LoginApi
{
}