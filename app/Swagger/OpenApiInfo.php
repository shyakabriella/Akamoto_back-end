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
    description: 'Register, login, and logout APIs'
)]
#[OA\Tag(
    name: 'Profile',
    description: 'User profile APIs'
)]
class OpenApiInfo
{
    #[OA\Post(
        path: '/api/register',
        tags: ['Authentication'],
        summary: 'Register new user',
        description: 'User fills name, email, and phone. System generates username and password.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'phone'],
                properties: [
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                        example: 'John Customer'
                    ),
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        example: 'john@example.com'
                    ),
                    new OA\Property(
                        property: 'phone',
                        type: 'string',
                        example: '250788123456'
                    ),
                    new OA\Property(
                        property: 'role',
                        type: 'string',
                        enum: ['admin', 'rider', 'customer'],
                        example: 'customer'
                    ),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'User account created successfully'),
            new OA\Response(response: 403, description: 'Only admin can create rider or admin accounts'),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 500, description: 'Role not found'),
        ]
    )]
    public function register(): void
    {
    }

    #[OA\Post(
        path: '/api/login',
        tags: ['Authentication'],
        summary: 'Login user',
        description: 'User logs in using phone number and generated password.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['phone', 'password'],
                properties: [
                    new OA\Property(
                        property: 'phone',
                        type: 'string',
                        example: '250788123456'
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        example: 'Aka-ABCD-1234'
                    ),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'User logged in successfully'),
            new OA\Response(response: 401, description: 'Invalid phone number or password'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function login(): void
    {
    }

    #[OA\Get(
        path: '/api/me',
        tags: ['Profile'],
        summary: 'Get logged-in user',
        description: 'Returns logged-in user with role and profile information.',
        security: [
            ['sanctum' => []],
        ],
        responses: [
            new OA\Response(response: 200, description: 'Logged-in user fetched successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function me(): void
    {
    }

    #[OA\Get(
        path: '/api/profile',
        tags: ['Profile'],
        summary: 'Get user profile',
        description: 'Returns the logged-in user profile.',
        security: [
            ['sanctum' => []],
        ],
        responses: [
            new OA\Response(response: 200, description: 'Profile fetched successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function getProfile(): void
    {
    }

    #[OA\Post(
        path: '/api/profile',
        tags: ['Profile'],
        summary: 'Create or update user profile',
        description: 'User updates profile image, location address, and street code.',
        security: [
            ['sanctum' => []],
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'image',
                            type: 'string',
                            format: 'binary'
                        ),
                        new OA\Property(
                            property: 'location_address',
                            type: 'string',
                            example: 'Kigali, Rwanda'
                        ),
                        new OA\Property(
                            property: 'street_code',
                            type: 'string',
                            example: 'KG 15 Ave'
                        ),
                    ],
                    type: 'object'
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Profile updated successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function updateProfile(): void
    {
    }

    #[OA\Delete(
        path: '/api/profile/image',
        tags: ['Profile'],
        summary: 'Delete profile image',
        description: 'Deletes only the logged-in user profile image.',
        security: [
            ['sanctum' => []],
        ],
        responses: [
            new OA\Response(response: 200, description: 'Profile image deleted successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'No profile image found'),
        ]
    )]
    public function deleteProfileImage(): void
    {
    }

    #[OA\Post(
        path: '/api/logout',
        tags: ['Authentication'],
        summary: 'Logout user',
        description: 'Deletes current user access token.',
        security: [
            ['sanctum' => []],
        ],
        responses: [
            new OA\Response(response: 200, description: 'User logged out successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function logout(): void
    {
    }
}