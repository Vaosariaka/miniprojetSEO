<?php

declare(strict_types=1);

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => (int) (getenv('DB_PORT') ?: 3306),
        'name' => getenv('DB_NAME') ?: 'news',
        'user' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'name' => 'Guerre en Iran',
    ],
];