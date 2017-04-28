<?php
return [
    'settings' => [

        'framework' => '{{FRAMEWORK_NAME}}', 
        
        'displayErrorDetails' => {{FRAMEWORK_ERRORS}},

        'db' => [
            'driver' => '{{DB_DRIVER}}', // Only MySQL Supported
            'host' => '{{DB_HOST}}', // Database Host
            'port' => {{DB_PORT}}, // My SQL Port
            'database' => '{{DB_NAME}}', // Database Name
            'username' => '{{DB_USER}}', // Database Username
            'password' => '{{DB_PASS}}', // Database Password
            'charset' => '{{DB_CHARSET}}',
            'collation' => '{{DB_COLLATION}}',
            'prefix' => '{{DB_PREFIX}}'
        ], 
        'view' => [
            'template_path' => __DIR__ . '/../views/',
            'twig' => [
                'cache' => {{TWIG_CACHE}}, //__DIR__ . '/../../storage/cache/twig'
                'debug' => {{TWIG_DEBUG}},
                'auto_reload' => true,
            ],
        ],
        'logger' => [
            'name' => '{{PROJECT_NAME}}',
            'log_path' => __DIR__ . '/../../storage/log/monolog/{{LOG_FILE_NAME}}.log', // PATH_TO_LOG
            'le_token' => {{LE_TOKEN}}, // Logentries Access Token
        ],
        'cloudinary' => [
            'enabled' => {{CLOUDINARY}}, // Enable Cloudinary
            'cloud_name' => '{{CL_CLOUD_NAME}}', // Cloud Name
            'api_key' => '{{CL_API_KEY}}', // API Key
            'api_secret' => '{{CL_API_SECRET}}', // API Secret
        ]
    ],
];