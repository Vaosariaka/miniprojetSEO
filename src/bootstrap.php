<?php

declare(strict_types=1);

session_start();

$config = require __DIR__ . '/config.php';
require __DIR__ . '/database.php';
require __DIR__ . '/helpers.php';

$pdo = create_pdo($config['db']);
initialize_database($pdo);
ensure_default_admin($pdo);
