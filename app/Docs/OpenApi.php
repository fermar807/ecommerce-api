<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'E-commerce API',
    description: 'API RESTful para gestion de un e-commerce'
)]
#[OA\Server(
    url: 'http://127.0.0.1:8000',
    description: 'Servidor local'
)]
class OpenApi
{
}
