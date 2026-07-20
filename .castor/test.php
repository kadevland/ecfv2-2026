<?php

declare(strict_types=1);

use Castor\Attribute\AsTask;
use Castor\Attribute\AsOption;
use Castor\Attribute\AsArgument;

use function Castor\io;
use function Castor\run;
use function Castor\capture;

// ============================================================================
// Test Commands - Using Docker with MongoDB support
// ============================================================================

#[AsTask(name: 'test', description: 'Run Pest tests with Docker (MongoDB + PostgreSQL support)')]
function runTests(
    #[AsArgument(description: 'Test file or filter (optional)')]
    ?string $filter = null,
    #[AsOption(description: 'Stop on first failure')]
    bool $stopOnFailure = false,
    #[AsOption(description: 'Run with coverage')]
    bool $coverage = false,
    #[AsOption(description: 'Run in parallel')]
    bool $parallel = false
): void {
    io()->title('🧪 Running Pest tests with Docker');

    // Check if test databases are running
    $pgRunning = str_contains(capture('docker ps'), 'cinephoria.postgres.test');
    $mongoRunning = str_contains(capture('docker ps'), 'cinephoria.mongodb.test');
    $appRunning = str_contains(capture('docker ps'), 'cinephoria.app-dev');

    if (!$pgRunning || !$mongoRunning) {
        io()->warning('Test databases not running. Starting them...');
        run('docker compose -f docker/docker-compose.test.yml up -d');
        io()->info('Waiting for databases to be ready...');
        sleep(8);
    }

    if (!$appRunning) {
        io()->error('❌ App container not running. Please run: castor docker:dev:up');
        exit(1);
    }

    // Build test command to run in existing container (uses phpunit.xml config)
    $cmd = 'docker exec cinephoria.app-dev';
    $cmd .= ' php -d memory_limit=1G vendor/bin/pest';

    // Add options
    if ($filter) {
        // Check if it's a file path or a filter
        if (file_exists($filter)) {
            $cmd .= ' ' . escapeshellarg($filter);
        } else {
            $cmd .= ' --filter=' . escapeshellarg($filter);
        }
    }

    if ($stopOnFailure) {
        $cmd .= ' --stop-on-failure';
    }

    if ($parallel) {
        $cmd .= ' --parallel';
    }

    if (!$coverage) {
        $cmd .= ' --no-coverage';
    }

    // Run tests
    io()->info('Running tests in cinephoria.app-dev container...');
    $result = run($cmd, allowFailure: true);

    if ($result->isSuccessful()) {
        io()->success('✅ Tests passed successfully!');
    } else {
        io()->error('❌ Some tests failed');
        exit(1);
    }
}

#[AsTask(name: 'test:unit', description: 'Run only unit tests')]
function testUnit(): void {
    io()->title('🧪 Running Unit tests');
    runTests('tests/Unit');
}

#[AsTask(name: 'test:feature', description: 'Run only feature tests')]
function testFeature(): void {
    io()->title('🧪 Running Feature tests');
    runTests('tests/Feature');
}

#[AsTask(name: 'test:employee', description: 'Run employee interface tests')]
function testEmployee(): void {
    io()->title('👷 Running Employee tests');
    runTests('tests/Feature/EspaceEmployeeTest.php');
}

#[AsTask(name: 'test:admin', description: 'Run admin interface tests')]
function testAdmin(): void {
    io()->title('👨‍💼 Running Admin tests');
    runTests('tests/Feature/EspaceAdminTest.php');
}

#[AsTask(name: 'test:reservation', description: 'Run reservation flow tests')]
function testReservation(): void {
    io()->title('🎫 Running Reservation tests');
    runTests('tests/Feature/ReservationCompleteTest.php');
}

#[AsTask(name: 'test:coverage', description: 'Run tests with coverage report')]
function testCoverage(): void {
    io()->title('📊 Running tests with coverage');
    runTests(coverage: true);
}

#[AsTask(name: 'test:db', description: 'Start test databases')]
function testDb(): void {
    io()->title('🗄️ Starting test databases');

    run('docker compose -f docker/docker-compose.test.yml up -d');

    io()->info('Waiting for databases to be ready...');
    sleep(5);

    // Check status
    run('docker compose -f docker/docker-compose.test.yml ps');

    io()->success('✅ Test databases are running');
    io()->info('PostgreSQL test: localhost:5433');
    io()->info('MongoDB test: localhost:27018');
}

#[AsTask(name: 'test:stop', description: 'Stop test databases')]
function testStop(): void {
    io()->title('🛑 Stopping test databases');

    run('docker compose -f docker/docker-compose.test.yml down');

    io()->success('✅ Test databases stopped');
}

#[AsTask(name: 'test:build', description: 'Build test Docker image with MongoDB support')]
function testBuild(): void {
    io()->title('🔨 Building test Docker image');

    run('docker build -f ./docker/services/frankenphp/Dockerfile --target development -t ecf-test .');

    io()->success('✅ Test image built: ecf-test');
}

#[AsTask(name: 'test:setup', description: 'Setup test databases with migrations')]
function testSetup(): void {
    io()->title('🏗️ Setting up test databases');

    // Start test databases if not running
    testDb();

    // Check if app container is running
    $appRunning = str_contains(capture('docker ps'), 'cinephoria.app-dev');
    if (!$appRunning) {
        io()->error('❌ App container not running. Please run: castor docker:dev:up');
        exit(1);
    }

    // Run migrations in test environment (using TEST databases)
    io()->info('Running migrations in test environment (tmpfs PostgreSQL)...');
    $migrateCmd = 'docker exec cinephoria.app-dev';
    $migrateCmd .= ' php artisan migrate --env=testing --force';

    run($migrateCmd);

    io()->success('✅ Test databases setup complete');
}
