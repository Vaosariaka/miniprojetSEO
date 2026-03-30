<?php

declare(strict_types=1);

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => (int) (getenv('DB_PORT') ?: 3306),
        'name' => getenv('DB_NAME') ?: 'iran_war_db',
        'user' => getenv('DB_USER') ?: 'iran_user',
        'password' => getenv('DB_PASSWORD') ?: 'iran_pass',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'name' => 'Guerre en Iran',
    ],
];
