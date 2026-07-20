<?php

declare(strict_types=1);

use Castor\Attribute\AsTask;

#[AsTask(namespace: 'shell', name: 'app', description: 'Open bash shell in application container')]
function openShellInApp(): void
{
    openShell(getDockerApp());
}

#[AsTask(namespace: 'shell', name: 'node', description: 'Open bash shell in node container')]
function openShellInNode(): void
{
    openShell(getDockerNode());
}

#[AsTask(namespace: 'shell', name: 'postgres', description: 'Open bash shell in postgres container')]
function openShellInPostgres(): void
{
    openShell(getDockerPostgres());
}

#[AsTask(namespace: 'shell', name: 'pgbouncer', description: 'Open bash shell in pgbouncer container')]
function openShellInPgBouncer(): void
{
    openShell(getDockerPGbouncer());
}

#[AsTask(namespace: 'shell', name: 'mongo', description: 'Open bash shell in mongodb container')]
function openShellInMongo(): void
{
    openShell(getDockerMongoDB());
}

#[AsTask(namespace: 'shell', name: 'redis', description: 'Open bash shell in redis container')]
function openShellInRedis(): void
{
    openShell(getDockerRedis());
}
