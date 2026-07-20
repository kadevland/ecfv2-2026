<?php

declare(strict_types=1);

define('DOCKER_PATH', __DIR__ . '/docker');
define('ROOT_PATH', __DIR__);
define('STORAGE_PATH', __DIR__ . '/storage');
define('LOGS_PATH', __DIR__ . '/storage/logs');
define('CACHE_PATH', __DIR__ . '/bootstrap/cache');

enum DockerContainerName: string
{
    case APP       = 'DOCKER_APP_NAME';
    case NODE      = 'DOCKER_NODE_NAME';
    case POSTGRES  = 'POSTGRES_CONTAINER_NAME';
    case PGBOUNCER = 'PGBOUNCER_CONTAINER_NAME';
    case MONGODB   = 'MONGODB_CONTAINER_NAME';
    case REDIS     = 'REDIS_CONTAINER_NAME';
}

enum EvnFiles: string
{
    case ENV         = '.env';
    case ENV_DOCKER  = '.env.docker';
    case ENV_LOCAL   = '.env.local';
    case ENV_TESTING = '.env.testing';

    /** @return array<self> */
    public static function prod(): array
    {
        return [
            self::ENV,
            self::ENV_DOCKER,
        ];
    }
}

// Load environment variables

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/.castor/bootstrap.php';
