<?php

declare(strict_types=1);

use function Castor\io;
use function Castor\run;

use Castor\Attribute\AsTask;
use Castor\Attribute\AsArgument;

// ============================================================================
// Abstract Docker Functions
// ============================================================================

/**
 * @param string|array<string> $dockerComposeFile
 */
function abstractDockerCommand(
    string|array $dockerComposeFile,
    string $command,
    string $options = ''
): void {
    $envPath = ROOT_PATH . '/' . EvnFiles::ENV_DOCKER->value;

    $composePath = is_array($dockerComposeFile)
        ? array_map(fn ($file) => getDockerPath() . '/' . $file, $dockerComposeFile)
        : getDockerPath() . '/' . $dockerComposeFile;

    $baseCmd = dockerComposeCmd($envPath, $composePath);
    $fullCmd = $baseCmd . ' ' . $command . (!empty($options) ? ' ' . $options : '');

    run($fullCmd);
}

// ============================================================================
// Executors for Docker Commands
// ============================================================================

/**
 * @param string|array<string> $dockerComposeFile
 */
function execDockerUp(string|array $dockerComposeFile, string $options = ''): void
{
    io()->title('🚀 Starting Docker containers');
    abstractDockerCommand($dockerComposeFile, 'up -d', $options);
    io()->success('✅ Containers started');
}

/**
 * @param string|array<string> $dockerComposeFile
 */
function execDockerDown(string|array $dockerComposeFile, string $options = ''): void
{
    io()->title('🛑 Stopping Docker containers');
    abstractDockerCommand($dockerComposeFile, 'down', $options);
    io()->success('✅ Containers stopped');
}

/**
 * @param string|array<string> $dockerComposeFile
 */
function execDockerBuild(string|array $dockerComposeFile, string $options = ''): void
{
    io()->title('🔨 Building Docker containers');
    abstractDockerCommand($dockerComposeFile, 'build', $options);
    io()->success('✅ Containers built');
}

/**
 * @param string|array<string> $dockerComposeFile
 */
function execDockerLogs(string|array $dockerComposeFile, string $options = ''): void
{
    io()->title('📋 Docker containers logs');
    abstractDockerCommand($dockerComposeFile, 'logs', $options);
}

/**
 * @param string|array<string> $dockerComposeFile
 */
function execDockerRestart(string|array $dockerComposeFile, string $options = ''): void
{
    io()->title('🔄 Restarting Docker containers');
    abstractDockerCommand($dockerComposeFile, 'restart', $options);
    io()->success('✅ Containers restarted');
}

// ============================================================================
// Development Environment Commands
// ============================================================================

#[AsTask(
    namespace: 'docker:dev', name: 'up', description: 'Start dev containers')]
function dockerDevUp(
    #[AsArgument(description: 'Additional options')]
    string $options = ''
): void {
    execDockerUp(['docker-compose.yml', 'docker-compose.dev.yml'], $options);
}

#[AsTask(
    namespace: 'docker:dev', name: 'down', description: 'Stop dev containers')]
function dockerDevDown(
    #[AsArgument(description: 'Remove volumes')]
    bool $volumes = false
): void {
    execDockerDown(['docker-compose.yml', 'docker-compose.dev.yml'], $volumes ? ' --volumes' : '');
}

#[AsTask(
    namespace: 'docker:dev', name: 'build', description: 'Build dev containers')]
function dockerDevBuild(
    #[AsArgument(description: 'Force rebuild')]
    bool $noCache = false
): void {
    execDockerBuild(['docker-compose.yml', 'docker-compose.dev.yml'], $noCache ? ' --no-cache' : '');
}

#[AsTask(
    namespace: 'docker:dev', name: 'rebuild', description: 'Rebuild and restart dev containers')]
function dockerDevRebuild(
    #[AsArgument(description: 'Force rebuild')]
    bool $noCache = false
): void {
    io()->title('🔄 Rebuilding Dev Docker environment');
    dockerDevDown();
    dockerDevBuild($noCache);
    dockerDevUp();
    io()->success('✅ Dev environment rebuilt');
}

#[AsTask(
    namespace: 'docker:dev', name: 'logs', description: 'Show dev containers logs')]
function dockerDevLogs(
    #[AsArgument(description: 'Service name')]
    string $service = '',
    #[AsArgument(description: 'Follow logs')]
    bool $follow = false,
    #[AsArgument(description: 'Lines to show')]
    int $tail = 100
): void {
    $options = '';
    if ($follow) {
        $options .= ' -f';
    }
    $options .= ' --tail=' . $tail;
    if (!empty($service)) {
        $options .= ' ' . $service;
    }

    execDockerLogs(['docker-compose.yml', 'docker-compose.dev.yml'], $options);
}

#[AsTask(
    namespace: 'docker:dev', name: 'restart', description: 'Restart dev containers')]
function dockerDevRestart(
    #[AsArgument(description: 'Service name')]
    string $service = ''
): void {
    execDockerRestart(['docker-compose.yml', 'docker-compose.dev.yml'], !empty($service) ? ' ' . $service : '');
}

// ============================================================================
// Production Environment Commands
// ============================================================================

#[AsTask(
    namespace: 'docker:prod', name: 'up', description: 'Start prod containers')]
function dockerProdUp(
    #[AsArgument(description: 'Additional options')]
    string $options = ''
): void {
    execDockerUp(['docker-compose.yml', 'docker-compose.prod.yml'], $options);
}

#[AsTask(
    namespace: 'docker:prod', name: 'down', description: 'Stop prod containers')]
function dockerProdDown(
    #[AsArgument(description: 'Remove volumes')]
    bool $volumes = false
): void {
    execDockerDown(['docker-compose.yml', 'docker-compose.prod.yml'], $volumes ? ' --volumes' : '');
}

#[AsTask(
    namespace: 'docker:prod', name: 'build', description: 'Build prod containers')]
function dockerProdBuild(
    #[AsArgument(description: 'Force rebuild')]
    bool $noCache = false
): void {
    execDockerBuild(['docker-compose.yml', 'docker-compose.prod.yml'], $noCache ? ' --no-cache' : '');
}

#[AsTask(
    namespace: 'docker:prod', name: 'rebuild', description: 'Rebuild and restart prod containers')]
function dockerProdRebuild(
    #[AsArgument(description: 'Force rebuild')]
    bool $noCache = false
): void {
    io()->title('🔄 Rebuilding Prod Docker environment');
    dockerProdDown();
    dockerProdBuild($noCache);
    dockerProdUp();
    io()->success('✅ Prod environment rebuilt');
}

#[AsTask(
    namespace: 'docker:prod', name: 'logs', description: 'Show prod containers logs')]
function dockerProdLogs(
    #[AsArgument(description: 'Service name')]
    string $service = '',
    #[AsArgument(description: 'Follow logs')]
    bool $follow = false,
    #[AsArgument(description: 'Lines to show')]
    int $tail = 100
): void {
    $options = '';
    if ($follow) {
        $options .= ' -f';
    }
    $options .= ' --tail=' . $tail;
    if (!empty($service)) {
        $options .= ' ' . $service;
    }

    execDockerLogs(['docker-compose.yml', 'docker-compose.prod.yml'], $options);
}

#[AsTask(
    namespace: 'docker:prod', name: 'restart', description: 'Restart prod containers')]
function dockerProdRestart(
    #[AsArgument(description: 'Service name')]
    string $service = ''
): void {
    execDockerRestart(['docker-compose.yml', 'docker-compose.prod.yml'], !empty($service) ? ' ' . $service : '');
}

// ============================================================================
// Test Environment Commands
// ============================================================================

#[AsTask(
    namespace: 'docker:test', name: 'up', description: 'Start test containers (PostgreSQL + MongoDB)')]
function dockerTestUp(
    #[AsArgument(description: 'Additional options')]
    string $options = ''
): void {
    io()->title('🧪 Starting Test containers');
    io()->writeln('Starting PostgreSQL test container on port 5433...');
    io()->writeln('Starting MongoDB test container on port 27018...');
    abstractDockerCommand('docker-compose.test.yml', 'up -d', $options);
    io()->success('✅ Test containers started and ready');
    io()->writeln('💡 Run tests with: vendor/bin/pest');
}

#[AsTask(
    namespace: 'docker:test', name: 'down', description: 'Stop test containers')]
function dockerTestDown(
    #[AsArgument(description: 'Remove volumes')]
    bool $volumes = false
): void {
    io()->title('🛑 Stopping Test containers');
    abstractDockerCommand('docker-compose.test.yml', 'down', $volumes ? ' --volumes' : '');
    io()->success('✅ Test containers stopped (test data cleared)');
}

#[AsTask(
    namespace: 'docker:test', name: 'restart', description: 'Restart test containers')]
function dockerTestRestart(): void
{
    io()->title('🔄 Restarting Test containers');
    dockerTestDown();
    dockerTestUp();
    io()->success('✅ Test containers restarted with fresh databases');
}

#[AsTask(
    namespace: 'docker:test', name: 'logs', description: 'Show test containers logs')]
function dockerTestLogs(
    #[AsArgument(description: 'Service name (postgres-test or mongodb-test)')]
    string $service = '',
    #[AsArgument(description: 'Follow logs')]
    bool $follow = false,
    #[AsArgument(description: 'Lines to show')]
    int $tail = 50
): void {
    $options = '';
    if ($follow) {
        $options .= ' -f';
    }
    $options .= ' --tail=' . $tail;
    if (!empty($service)) {
        $options .= ' ' . $service;
    }

    execDockerLogs('docker-compose.test.yml', $options);
}

#[AsTask(
    namespace: 'docker:test', name: 'status', description: 'Show test containers status')]
function dockerTestStatus(): void
{
    io()->title('🧪 Test Containers Status');

    $containers = [
        'PostgreSQL Test (5433)' => 'cinephoria.postgres.test',
        'MongoDB Test (27018)'    => 'cinephoria.mongodb.test',
    ];

    foreach ($containers as $name => $container) {
        $status = isContainerRunning($container) ? '🟢 Running' : '🔴 Stopped';
        io()->writeln(sprintf('%-25s: %s', $name, $status));
    }

    io()->newLine();
    io()->writeln('💡 Commands:');
    io()->writeln('  castor docker:test:up    - Start test containers');
    io()->writeln('  vendor/bin/pest          - Run tests');
    io()->writeln('  castor docker:test:down  - Stop test containers');
}

#[AsTask(
    namespace: 'docker:test', name: 'psql', description: 'Connect to test PostgreSQL')]
function dockerTestPsql(): void
{
    io()->title('🐘 Connecting to Test PostgreSQL');
    io()->writeln('Database: cinephoria_test | User: test_user');
    run('docker exec -it cinephoria.postgres.test psql -U test_user -d cinephoria_test');
}

#[AsTask(
    namespace: 'docker:test', name: 'mongo', description: 'Connect to test MongoDB')]
function dockerTestMongo(): void
{
    io()->title('🍃 Connecting to Test MongoDB');
    io()->writeln('Database: cinephoria_read_test | User: test_user');
    run('docker exec -it cinephoria.mongodb.test mongosh -u test_user -p test_password');
}

// ============================================================================
// Common Docker Commands (Environment Agnostic)
// ============================================================================

#[AsTask(
    namespace: 'docker', name: 'ps', description: 'Show running Docker containers')]
function dockerPs(): void
{
    io()->title('📊 Docker containers status');
    run('docker ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"');
}

#[AsTask(
    namespace: 'docker', name: 'status', description: 'Show detailed status of all containers')]
function dockerStatus(): void
{
    io()->title('🔍 Docker Environment Status');

    $containers = [
        'App'        => getDockerApp(),
        'Node'       => getDockerNode(),
        'PostgreSQL' => getDockerPostgres(),
        'PgBouncer'  => getDockerPGbouncer(),
        'MongoDB'    => getDockerMongoDB(),
        'Redis'      => getDockerRedis(),
    ];

    foreach ($containers as $name => $container) {
        $status = isContainerRunning($container) ? '🟢 Running' : '🔴 Stopped';
        io()->writeln(sprintf('%-12s: %s (%s)', $name, $status, $container));
    }
}
