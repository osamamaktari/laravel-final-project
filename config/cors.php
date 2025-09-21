                    <?php
                    return [
                        'paths' => [
                            'api/v1/*',
                            'sanctum/csrf-cookie',
                            'login',
                            'logout',
                            'register',
                            'user',
                        ],

                        'allowed_methods' => ['*'],
                        'allowed_origins' => ['http://localhost:5173'],
                        'allowed_origins_patterns' => [],
                        'allowed_headers' => ['*'],
                        'exposed_headers' => [],
                        'max_age' => 0,
                        'supports_credentials' => true,
                    ];
