<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Akamoto API Documentation',
    description: 'Akamoto is a city delivery system with customer, rider, and admin users.'
)]
#[OA\Server(
    url: 'https://api.icotrix.com',
    description: 'Production API Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'apiKey',
    name: 'Authorization',
    in: 'header',
    description: 'Enter token like: Bearer YOUR_TOKEN_HERE'
)]
#[OA\Tag(
    name: 'Authentication',
    description: 'Register and login APIs'
)]
#[OA\Tag(
    name: 'Profile',
    description: 'User profile APIs'
)]
class OpenApiInfo
{
}