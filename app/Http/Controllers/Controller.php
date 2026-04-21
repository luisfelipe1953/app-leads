<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'OMC Leads API',
    version: '1.0.0',
    description: 'API REST para gestión de leads de One Million Copy SAS. Autenticación via header X-API-KEY.',
    contact: new OA\Contact(email: 'admin@omc.com')
)]
#[OA\Server(url: L5_SWAGGER_CONST_HOST, description: 'Servidor local')]
#[OA\SecurityScheme(
    securityScheme: 'apiKey',
    type: 'apiKey',
    in: 'header',
    name: 'X-API-KEY',
    description: 'API Key requerida. Configura APP_API_KEY en .env'
)]
abstract class Controller
{
}
