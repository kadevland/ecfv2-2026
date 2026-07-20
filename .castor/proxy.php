<?php

declare(strict_types=1);
use function Castor\run;

use Castor\Attribute\AsTask;
use Castor\Attribute\AsArgument;
use Castor\Attribute\AsRawTokens;

#[AsTask(name: 'artisan', description: 'Execute Artisan command in container')]
function runArtisanInApp(
    #[AsRawTokens] array $args
): void {
    // Joindre tous les arguments pour former la commande artisan complète
    $artisanCmd = !empty($args) ? 'php artisan ' . implode(' ', $args) : 'php artisan';

    $containerName = getDockerApp();
    $dockerCommand = dockerExecCmd($containerName, $artisanCmd);

    run($dockerCommand);
}

#[AsTask(name: 'node', description: 'Execute Node command in container')]
function runNodeInApp(
    #[AsArgument(description: 'The node command to run')]
    string $cmd = ''
): void {
    $nodeCmd       = !empty($cmd) ? "node {$cmd}" : 'node --help';
    $containerName = getDockerNode();
    $dockerCommand = dockerExecCmd($containerName, $nodeCmd);

    run($dockerCommand);
}

#[AsTask(name: 'npm', description: 'Execute NPM command in container')]
function runNpmInApp(
    #[AsArgument(description: 'The npm command to run')]
    string $cmd = ''
): void {
    $npmCmd        = !empty($cmd) ? "npm {$cmd}" : 'npm --help';
    $containerName = getDockerNode();
    $dockerCommand = dockerExecCmd($containerName, $npmCmd);

    run($dockerCommand);
}

#[AsTask(name: 'debug-env', description: 'Show all environment variables seen by Castor')]
function debugEnvironment(): void
{
    echo "=== Environment variables seen by Castor ===\n";

    $envVars = [
        'DOCKER_APP_NAME',
        'DOCKER_NODE_NAME',
        'POSTGRES_CONTAINER_NAME',
        'PGBOUNCER_CONTAINER_NAME',
        'MONGODB_CONTAINER_NAME',
        'REDIS_CONTAINER_NAME',
    ];

    foreach ($envVars as $var) {
        $value = getEnvValue($var, 'NOT_FOUND');
        echo "{$var} = '{$value}'\n";
    }

    echo "\n=== Testing getDockerNode() ===\n";
    echo "getDockerNode() = '" . getDockerNode() . "'\n";
}

// #[AsTask(name: 'shell', description: 'Open bash shell in container')]

// #[AsTask(name: 'psql', description: 'Open PostgreSQL shell')]
// function openPsqlShell (?string $command = null) : string
// {
//     $user = getEnvValue('POSTGRES_USER', 'app_user');
//     $db   = getEnvValue('POSTGRES_DB', 'app_user');

//     $psqlCmd = $command
//         ? "psql -U {$user} -d {$db} -c \"{$command}\""
//         : "psql -U {$user} -d {$db}";
//     return runInContainer(DockerContainerName::POSTGRES, $psqlCmd);
// }
