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
#[OA\Tag(
    name: 'Rider',
    description: 'Rider profile, online status, and GPS location APIs'
)]
#[OA\Tag(
    name: 'Admin Riders',
    description: 'Admin rider management APIs'
)]
#[OA\Tag(
    name: 'Pricing',
    description: 'Delivery price calculation APIs'
)]
#[OA\Tag(
    name: 'Admin Pricing Rules',
    description: 'Admin pricing and commission management APIs'
)]
class OpenApiInfo
{
    /*
    |--------------------------------------------------------------------------
    | Authentication APIs
    |--------------------------------------------------------------------------
    */

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
                    new OA\Property(property: 'name', type: 'string', example: 'John Customer'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                    new OA\Property(property: 'phone', type: 'string', example: '250788123456'),
                    new OA\Property(property: 'role', type: 'string', enum: ['admin', 'rider', 'customer'], example: 'customer'),
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
                    new OA\Property(property: 'phone', type: 'string', example: '250788123456'),
                    new OA\Property(property: 'password', type: 'string', example: 'Aka-ABCD-1234'),
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

    /*
    |--------------------------------------------------------------------------
    | Profile APIs
    |--------------------------------------------------------------------------
    */

    #[OA\Get(
        path: '/api/me',
        tags: ['Profile'],
        summary: 'Get logged-in user',
        description: 'Returns logged-in user with role, profile, and rider information.',
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
                        new OA\Property(property: 'image', type: 'string', format: 'binary'),
                        new OA\Property(property: 'location_address', type: 'string', example: 'Kigali, Rwanda'),
                        new OA\Property(property: 'street_code', type: 'string', example: 'KG 15 Ave'),
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

    /*
    |--------------------------------------------------------------------------
    | Rider APIs
    |--------------------------------------------------------------------------
    */

    #[OA\Get(
        path: '/api/rider/profile',
        tags: ['Rider'],
        summary: 'Get rider profile',
        description: 'Returns the logged-in rider profile. Only rider users can access this endpoint.',
        security: [
            ['sanctum' => []],
        ],
        responses: [
            new OA\Response(response: 200, description: 'Rider profile fetched successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Only rider users can access rider profile'),
        ]
    )]
    public function getRiderProfile(): void
    {
    }

    #[OA\Post(
        path: '/api/rider/profile',
        tags: ['Rider'],
        summary: 'Create or update rider profile',
        description: 'Rider creates or updates transport and identity information. Status starts as pending until admin approves.',
        security: [
            ['sanctum' => []],
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['vehicle_type'],
                properties: [
                    new OA\Property(property: 'vehicle_type', type: 'string', enum: ['moto', 'bicycle', 'car', 'van'], example: 'moto'),
                    new OA\Property(property: 'vehicle_plate_number', type: 'string', example: 'RAE 123 A'),
                    new OA\Property(property: 'vehicle_color', type: 'string', example: 'Black'),
                    new OA\Property(property: 'national_id', type: 'string', example: '1199880011223344'),
                    new OA\Property(property: 'driving_license_number', type: 'string', example: 'DL-123456'),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Rider profile saved successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Only rider users can create or update rider profile'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function storeOrUpdateRiderProfile(): void
    {
    }

    #[OA\Post(
        path: '/api/rider/go-online',
        tags: ['Rider'],
        summary: 'Rider goes online',
        description: 'Approved rider changes status to online so the system can match them with customer orders.',
        security: [
            ['sanctum' => []],
        ],
        responses: [
            new OA\Response(response: 200, description: 'Rider is now online'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Rider not approved or not allowed'),
            new OA\Response(response: 404, description: 'Rider profile not found'),
        ]
    )]
    public function riderGoOnline(): void
    {
    }

    #[OA\Post(
        path: '/api/rider/go-offline',
        tags: ['Rider'],
        summary: 'Rider goes offline',
        description: 'Rider changes status to offline so the system stops matching them with new orders.',
        security: [
            ['sanctum' => []],
        ],
        responses: [
            new OA\Response(response: 200, description: 'Rider is now offline'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Only rider users can go offline'),
            new OA\Response(response: 404, description: 'Rider profile not found'),
        ]
    )]
    public function riderGoOffline(): void
    {
    }

    #[OA\Post(
        path: '/api/rider/location',
        tags: ['Rider'],
        summary: 'Update rider GPS location',
        description: 'Approved rider updates current latitude, longitude, and optional current address.',
        security: [
            ['sanctum' => []],
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['current_latitude', 'current_longitude'],
                properties: [
                    new OA\Property(property: 'current_latitude', type: 'number', format: 'float', example: -1.9441),
                    new OA\Property(property: 'current_longitude', type: 'number', format: 'float', example: 30.0619),
                    new OA\Property(property: 'current_address', type: 'string', example: 'Kigali City Center'),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Rider location updated successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Rider not approved or not allowed'),
            new OA\Response(response: 404, description: 'Rider profile not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function updateRiderLocation(): void
    {
    }

    /*
    |--------------------------------------------------------------------------
    | Admin Rider Management APIs
    |--------------------------------------------------------------------------
    */

    #[OA\Get(
        path: '/api/admin/riders',
        tags: ['Admin Riders'],
        summary: 'List riders',
        description: 'Admin can list riders and filter by status, online state, or search keyword.',
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'status',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['pending', 'approved', 'rejected', 'suspended']),
                example: 'pending'
            ),
            new OA\Parameter(
                name: 'is_online',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean'),
                example: false
            ),
            new OA\Parameter(
                name: 'search',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'Rider One'
            ),
            new OA\Parameter(
                name: 'per_page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: 15
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Riders fetched successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Only admin can view riders'),
        ]
    )]
    public function adminListRiders(): void
    {
    }

    #[OA\Get(
        path: '/api/admin/riders/{rider}',
        tags: ['Admin Riders'],
        summary: 'Show rider details',
        description: 'Admin can view one rider by rider ID.',
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'rider',
                in: 'path',
                required: true,
                description: 'Rider ID',
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Rider details fetched successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Only admin can view rider details'),
            new OA\Response(response: 404, description: 'Rider not found'),
        ]
    )]
    public function adminShowRider(): void
    {
    }

    #[OA\Post(
        path: '/api/admin/riders/{rider}/approve',
        tags: ['Admin Riders'],
        summary: 'Approve rider',
        description: 'Admin approves a pending rider. After approval, rider can go online.',
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'rider',
                in: 'path',
                required: true,
                description: 'Rider ID',
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'admin_notes', type: 'string', example: 'Rider documents checked and approved.'),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Rider approved successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Only admin can approve riders'),
            new OA\Response(response: 404, description: 'Rider not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function adminApproveRider(): void
    {
    }

    #[OA\Post(
        path: '/api/admin/riders/{rider}/reject',
        tags: ['Admin Riders'],
        summary: 'Reject rider',
        description: 'Admin rejects a rider profile and provides rejection reason.',
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'rider',
                in: 'path',
                required: true,
                description: 'Rider ID',
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['rejection_reason'],
                properties: [
                    new OA\Property(property: 'rejection_reason', type: 'string', example: 'Driving license document is missing.'),
                    new OA\Property(property: 'admin_notes', type: 'string', example: 'Ask rider to upload complete documents.'),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Rider rejected successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Only admin can reject riders'),
            new OA\Response(response: 404, description: 'Rider not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function adminRejectRider(): void
    {
    }

    #[OA\Post(
        path: '/api/admin/riders/{rider}/suspend',
        tags: ['Admin Riders'],
        summary: 'Suspend rider',
        description: 'Admin suspends an approved rider. Suspended rider is forced offline.',
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'rider',
                in: 'path',
                required: true,
                description: 'Rider ID',
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'admin_notes', type: 'string', example: 'Suspended because of customer complaint.'),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Rider suspended successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Only admin can suspend riders'),
            new OA\Response(response: 404, description: 'Rider not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function adminSuspendRider(): void
    {
    }

    /*
    |--------------------------------------------------------------------------
    | Pricing Quote API
    |--------------------------------------------------------------------------
    */

    #[OA\Post(
        path: '/api/pricing/quote',
        tags: ['Pricing'],
        summary: 'Calculate delivery price',
        description: 'Calculates delivery price, Akamoto commission, and rider earning using the active pricing rule.',
        security: [
            ['sanctum' => []],
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['distance_km'],
                properties: [
                    new OA\Property(property: 'distance_km', type: 'number', format: 'float', example: 6),
                    new OA\Property(property: 'vehicle_type', type: 'string', enum: ['moto', 'bicycle', 'car', 'van'], example: 'moto'),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Delivery price calculated successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'No active pricing rule found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function calculatePricingQuote(): void
    {
    }

    /*
    |--------------------------------------------------------------------------
    | Admin Pricing Rules APIs
    |--------------------------------------------------------------------------
    */

    #[OA\Get(
        path: '/api/admin/pricing-rules',
        tags: ['Admin Pricing Rules'],
        summary: 'List pricing rules',
        description: 'Admin can list all pricing rules and filter by vehicle type, active status, or search keyword.',
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'vehicle_type',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['moto', 'bicycle', 'car', 'van']),
                example: 'moto'
            ),
            new OA\Parameter(
                name: 'is_active',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean'),
                example: true
            ),
            new OA\Parameter(
                name: 'search',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'Kigali Moto'
            ),
            new OA\Parameter(
                name: 'per_page',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: 15
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Pricing rules fetched successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Only admin can view pricing rules'),
        ]
    )]
    public function adminListPricingRules(): void
    {
    }

    #[OA\Post(
        path: '/api/admin/pricing-rules',
        tags: ['Admin Pricing Rules'],
        summary: 'Create pricing rule',
        description: 'Admin creates a new pricing rule with base price, price per kilometer, minimum price, and commission percentage.',
        security: [
            ['sanctum' => []],
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'base_price', 'price_per_km', 'minimum_price', 'commission_percentage'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Kigali Moto Standard'),
                    new OA\Property(property: 'vehicle_type', type: 'string', enum: ['moto', 'bicycle', 'car', 'van'], example: 'moto'),
                    new OA\Property(property: 'base_price', type: 'number', format: 'float', example: 1000),
                    new OA\Property(property: 'price_per_km', type: 'number', format: 'float', example: 500),
                    new OA\Property(property: 'minimum_price', type: 'number', format: 'float', example: 1500),
                    new OA\Property(property: 'commission_percentage', type: 'number', format: 'float', example: 20),
                    new OA\Property(property: 'currency', type: 'string', example: 'RWF'),
                    new OA\Property(property: 'is_active', type: 'boolean', example: true),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Pricing rule created successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Only admin can create pricing rules'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function adminCreatePricingRule(): void
    {
    }

    #[OA\Get(
        path: '/api/admin/pricing-rules/{pricing_rule}',
        tags: ['Admin Pricing Rules'],
        summary: 'Show pricing rule',
        description: 'Admin can view one pricing rule by ID.',
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'pricing_rule',
                in: 'path',
                required: true,
                description: 'Pricing rule ID',
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Pricing rule details fetched successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Only admin can view pricing rule details'),
            new OA\Response(response: 404, description: 'Pricing rule not found'),
        ]
    )]
    public function adminShowPricingRule(): void
    {
    }

    #[OA\Put(
        path: '/api/admin/pricing-rules/{pricing_rule}',
        tags: ['Admin Pricing Rules'],
        summary: 'Update pricing rule',
        description: 'Admin updates pricing rule values.',
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'pricing_rule',
                in: 'path',
                required: true,
                description: 'Pricing rule ID',
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Kigali Moto Updated'),
                    new OA\Property(property: 'vehicle_type', type: 'string', enum: ['moto', 'bicycle', 'car', 'van'], example: 'moto'),
                    new OA\Property(property: 'base_price', type: 'number', format: 'float', example: 1200),
                    new OA\Property(property: 'price_per_km', type: 'number', format: 'float', example: 600),
                    new OA\Property(property: 'minimum_price', type: 'number', format: 'float', example: 1800),
                    new OA\Property(property: 'commission_percentage', type: 'number', format: 'float', example: 18),
                    new OA\Property(property: 'currency', type: 'string', example: 'RWF'),
                    new OA\Property(property: 'is_active', type: 'boolean', example: true),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Pricing rule updated successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Only admin can update pricing rules'),
            new OA\Response(response: 404, description: 'Pricing rule not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function adminUpdatePricingRule(): void
    {
    }

    #[OA\Patch(
        path: '/api/admin/pricing-rules/{pricing_rule}',
        tags: ['Admin Pricing Rules'],
        summary: 'Partially update pricing rule',
        description: 'Admin partially updates pricing rule values.',
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'pricing_rule',
                in: 'path',
                required: true,
                description: 'Pricing rule ID',
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Kigali Moto Updated'),
                    new OA\Property(property: 'base_price', type: 'number', format: 'float', example: 1200),
                    new OA\Property(property: 'price_per_km', type: 'number', format: 'float', example: 600),
                    new OA\Property(property: 'minimum_price', type: 'number', format: 'float', example: 1800),
                    new OA\Property(property: 'commission_percentage', type: 'number', format: 'float', example: 18),
                    new OA\Property(property: 'is_active', type: 'boolean', example: true),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Pricing rule updated successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Only admin can update pricing rules'),
            new OA\Response(response: 404, description: 'Pricing rule not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function adminPatchPricingRule(): void
    {
    }

    #[OA\Post(
        path: '/api/admin/pricing-rules/{pricingRule}/activate',
        tags: ['Admin Pricing Rules'],
        summary: 'Activate pricing rule',
        description: 'Admin activates a pricing rule. Other active rules for the same vehicle type are deactivated.',
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'pricingRule',
                in: 'path',
                required: true,
                description: 'Pricing rule ID',
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Pricing rule activated successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Only admin can activate pricing rules'),
            new OA\Response(response: 404, description: 'Pricing rule not found'),
        ]
    )]
    public function adminActivatePricingRule(): void
    {
    }

    #[OA\Delete(
        path: '/api/admin/pricing-rules/{pricing_rule}',
        tags: ['Admin Pricing Rules'],
        summary: 'Delete pricing rule',
        description: 'Admin deletes a pricing rule.',
        security: [
            ['sanctum' => []],
        ],
        parameters: [
            new OA\Parameter(
                name: 'pricing_rule',
                in: 'path',
                required: true,
                description: 'Pricing rule ID',
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Pricing rule deleted successfully'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 403, description: 'Only admin can delete pricing rules'),
            new OA\Response(response: 404, description: 'Pricing rule not found'),
        ]
    )]
    public function adminDeletePricingRule(): void
    {
    }
}