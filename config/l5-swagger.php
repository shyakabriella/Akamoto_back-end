<?php

return [
    'default' => 'default',

    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'Akamoto API Documentation',
            ],

            'routes' => [
                /*
                 * Route for accessing API documentation interface.
                 */
                'api' => 'api/documentation',
            ],

            'paths' => [
                /*
                 * Use full URL for Swagger UI assets.
                 */
                'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', true),

                /*
                 * Path where Swagger UI assets are stored.
                 */
                'swagger_ui_assets_path' => env(
                    'L5_SWAGGER_UI_ASSETS_PATH',
                    'vendor/swagger-api/swagger-ui/dist/'
                ),

                /*
                 * File name of the generated JSON documentation file.
                 */
                'docs_json' => 'api-docs.json',

                /*
                 * File name of the generated YAML documentation file.
                 */
                'docs_yaml' => 'api-docs.yaml',

                /*
                 * Set this to json or yaml.
                 */
                'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),

                /*
                 * Paths where Swagger annotations/attributes are stored.
                 *
                 * app/Swagger must contain OpenApiInfo.php with OA Info.
                 * app/Http/Controllers/API contains endpoint docs.
                 */
                'annotations' => [
                    app_path('Swagger'),
                    app_path('Http/Controllers/API'),
                ],
            ],
        ],
    ],

    'defaults' => [
        'routes' => [
            /*
             * Route for accessing parsed Swagger annotations.
             */
            'docs' => 'docs',

            /*
             * Route for OAuth2 authentication callback.
             */
            'oauth2_callback' => 'api/oauth2-callback',

            /*
             * Middleware for Swagger routes.
             */
            'middleware' => [
                'api' => [],
                'asset' => [],
                'docs' => [],
                'oauth2_callback' => [],
            ],

            /*
             * Route group options.
             */
            'group_options' => [],
        ],

        'paths' => [
            /*
             * Location where parsed Swagger docs will be stored.
             */
            'docs' => storage_path('api-docs'),

            /*
             * Location where Swagger views are stored.
             */
            'views' => base_path('resources/views/vendor/l5-swagger'),

            /*
             * API base path.
             */
            'base' => env('L5_SWAGGER_BASE_PATH', null),

            /*
             * Deprecated excludes option.
             * Use scanOptions.exclude instead.
             */
            'excludes' => [],
        ],

        'scanOptions' => [
            /*
             * Optional custom generator factory.
             */
            'generator_factory' => null,

            /*
             * Default processors configuration.
             */
            'default_processors_configuration' => [
                /*
                 * Example:
                 * 'operationId.hash' => true,
                 */
            ],

            /*
             * Analyser.
             */
            'analyser' => null,

            /*
             * Analysis.
             */
            'analysis' => null,

            /*
             * Custom processors.
             */
            'processors' => [
                // \App\SwaggerProcessors\SchemaQueryParameter::class,
            ],

            /*
             * File pattern to scan.
             */
            'pattern' => null,

            /*
             * Directories to exclude from scanning.
             */
            'exclude' => [],

            /*
             * OpenAPI version.
             */
            'open_api_spec_version' => env(
                'L5_SWAGGER_OPEN_API_SPEC_VERSION',
                \L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION
            ),
        ],

        /*
         * API security definitions.
         */
        'securityDefinitions' => [
            'securitySchemes' => [
                /*
                 * Laravel Sanctum Bearer Token.
                 *
                 * In Swagger Authorize button, enter:
                 * Bearer YOUR_TOKEN_HERE
                 */
                'sanctum' => [
                    'type' => 'apiKey',
                    'description' => 'Enter token in format: Bearer YOUR_TOKEN_HERE',
                    'name' => 'Authorization',
                    'in' => 'header',
                ],
            ],

            'security' => [
                [
                    'sanctum' => [],
                ],
            ],
        ],

        /*
         * Regenerate docs automatically in development.
         */
        'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', true),

        /*
         * Generate YAML copy.
         */
        'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false),

        /*
         * Proxy settings.
         */
        'proxy' => false,

        /*
         * Additional Swagger UI config URL.
         */
        'additional_config_url' => null,

        /*
         * Sort operations.
         */
        'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),

        /*
         * Swagger validator URL.
         */
        'validator_url' => null,

        /*
         * Swagger UI configuration.
         */
        'ui' => [
            'display' => [
                'dark_mode' => env('L5_SWAGGER_UI_DARK_MODE', false),

                /*
                 * Options: list, full, none.
                 */
                'doc_expansion' => env('L5_SWAGGER_UI_DOC_EXPANSION', 'none'),

                /*
                 * Enable search/filter in Swagger UI.
                 */
                'filter' => env('L5_SWAGGER_UI_FILTERS', true),
            ],

            'authorization' => [
                /*
                 * Keep authorization token after browser refresh.
                 */
                'persist_authorization' => env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION', true),

                'oauth2' => [
                    /*
                     * Enable PKCE for OAuth2 authorization code flow.
                     */
                    'use_pkce_with_authorization_code_grant' => false,
                ],
            ],
        ],

        /*
         * Constants used in annotations.
         */
        'constants' => [
            'L5_SWAGGER_CONST_HOST' => env(
                'L5_SWAGGER_CONST_HOST',
                'http://127.0.0.1:8000'
            ),
        ],
    ],
];
