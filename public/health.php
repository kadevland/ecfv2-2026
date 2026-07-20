<?php

declare(strict_types=1);
header('Content-Type: application/json');

$checks = [
    'php_version'     => PHP_VERSION,
    'frankenphp'      => php_sapi_name(),
    'document_root'   => $_SERVER['DOCUMENT_ROOT'] ?? 'not set',
    'script_filename' => $_SERVER['SCRIPT_FILENAME'] ?? 'not set',
    'extensions'      => [
        'pdo'       => extension_loaded('pdo'),
        'pdo_pgsql' => extension_loaded('pdo_pgsql'),
        'redis'     => extension_loaded('redis'),
        'mongodb'   => extension_loaded('mongodb'),
    ],
    'env_vars' => [
        'APP_NAME' => $_ENV['APP_NAME'] ?? 'not set',
        'APP_ENV'  => $_ENV['APP_ENV'] ?? 'not set',
        'DB_HOST'  => $_ENV['DB_HOST'] ?? 'not set',
    ],
    'file_checks' => [
        'index.php' => file_exists(__DIR__ . '/index.php'),
        '.env'      => file_exists(dirname(__DIR__) . '/.env'),
        'vendor'    => is_dir(dirname(__DIR__) . '/vendor'),
    ],
];

echo json_encode($checks, JSON_PRETTY_PRINT);
