<?php

// Helper functions for Castor

declare(strict_types=1);

use function Castor\run;
use function Castor\context;
use function Castor\variable;

function getEnvValue(string $key, string $default = ''): mixed
{

    return variable($key, $default);
}

function getDockerContainerName(DockerContainerName $container, string $default = ''): string
{
    return (string) getEnvValue($container->value, $default);
}

function getDockerApp(): string
{
    return getDockerContainerName(DockerContainerName::APP, 'app');
}

function getDockerNode(): string
{
    return getDockerContainerName(DockerContainerName::NODE, 'node');
}

function getDockerPostgres(): string
{
    return getDockerContainerName(DockerContainerName::POSTGRES, 'postgres');
}

function getDockerPGbouncer(): string
{
    return getDockerContainerName(DockerContainerName::PGBOUNCER, 'pgbouncer');
}

function getDockerMongoDB(): string
{
    return getDockerContainerName(DockerContainerName::MONGODB, 'mongodb');
}

function getDockerRedis(): string
{
    return getDockerContainerName(DockerContainerName::REDIS, 'redis');
}

function dockerExecCmd(string $container, string $command, string $workdir = '/app'): string
{
    return sprintf('docker exec -w %s %s %s', escapeshellarg($workdir), escapeshellarg($container), $command);
}

function dockerExecInteractiveCmd(string $container, string $command, string $workdir = '/app'): string
{
    return sprintf('docker exec -it -w %s %s %s', escapeshellarg($workdir), escapeshellarg($container), $command);
}

/**
 * @param string|array<string> $dockercomposePaths
 */
function dockerComposeCmd(string $envPath, string|array $dockercomposePaths): string
{
    $dockercomposePath = is_array($dockercomposePaths) ? $dockercomposePaths : [$dockercomposePaths];

    $overwritePath = implode(' -f ', array_map(fn ($path) => escapeshellarg($path), $dockercomposePath));

    return sprintf('docker compose --env-file %s -f %s', escapeshellarg($envPath), $overwritePath);
}

// Path & File Helpers
function getProjectRoot(): string
{
    return realpath(DOCKER_PATH);
}

function getDockerPath(): string
{
    return realpath(DOCKER_PATH);
}

function ensureDirectoryExists(string $path): void
{
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
}

function copyEnvExample(string $name): void
{
    $projectRoot = getProjectRoot();
    $envExample  = $projectRoot . '/' . $name . '.example';
    $envTarget   = $projectRoot . '/' . $name;

    if (!file_exists($envTarget) && file_exists($envExample)) {
        copy($envExample, $envTarget);
    }
}

// Container Helpers
function isContainerRunning(string|DockerContainerName $containerName): bool
{
    $name = $containerName instanceof DockerContainerName
        ? getDockerContainerName($containerName)
        : $containerName;

    try {
        run(sprintf('docker exec %s echo "ok"', escapeshellarg($name)), context: context()->withQuiet());

        return true;
    } catch (\Throwable) {
        return false;
    }

}

function runInteractiveInContainer(string|DockerContainerName $containerName, string $command): string
{
    $name = $containerName instanceof DockerContainerName
        ? getDockerContainerName($containerName)
        : $containerName;

    if (!isContainerRunning($name)) {
        throw new \RuntimeException("Container '{$name}' is not running");
    }

    $dockerCommand = dockerExecInteractiveCmd($name, $command);

    return run($dockerCommand)->getOutput();
}

function runInContainer(string|DockerContainerName $containerName, string $command): string
{
    $name = $containerName instanceof DockerContainerName
        ? getDockerContainerName($containerName)
        : $containerName;

    if (!isContainerRunning($name)) {
        throw new \RuntimeException("Container '{$name}' is not running");
    }

    $dockerCommand = dockerExecCmd($name, $command);

    return run($dockerCommand)->getOutput();
}

function openShell(
    string|DockerContainerName $containerName
): void {

    $name = $containerName instanceof DockerContainerName
        ? getDockerContainerName($containerName)
        : $containerName;

    runInteractiveInContainer($name, 'bash');

}
