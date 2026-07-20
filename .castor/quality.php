<?php

declare(strict_types=1);

use Castor\Attribute\AsTask;
use Castor\Attribute\AsOption;

use function Castor\io;
use function Castor\run;

// ============================================================================
// Quality Assurance Commands
// ============================================================================

#[AsTask(name: 'quality:lint', description: 'Check code style with Pint (test mode)')]
function qualityLint (
    #[AsOption(description: 'Run command inside Docker container')]
    bool $docker = false
) : void {
    io()->title('🎨 Checking code style with Pint...');

    if ($docker) {
        runInContainer(getDockerApp(), 'vendor/bin/pint --test');
    } else {
        run('./vendor/bin/pint --test');
    }

    io()->success('✅ Code style is valid');
}

#[AsTask(name: 'quality:lint:fix', description: 'Fix code style with Pint')]
function qualityLintFix (
    #[AsOption(description: 'Run command inside Docker container')]
    bool $docker = false
) : void {
    io()->title('🎨 Fixing code style with Pint...');

    if ($docker) {
        runInContainer(getDockerApp(), 'vendor/bin/pint');
    } else {
        run('./vendor/bin/pint');
    }

    io()->success('✅ Code style fixed with Pint');
}

#[AsTask(name: 'quality:analyse', description: 'Run Larastan static analysis')]
function qualityAnalyse (
    #[AsOption(description: 'Run command inside Docker container')]
    bool $docker = false
) : void {
    io()->title('🔍 Running Larastan analysis...');

    if ($docker) {
        runInContainer(getDockerApp(), 'vendor/bin/phpstan analyse');
    } else {
        run('./vendor/bin/phpstan analyse');
    }

    io()->success('✅ Static analysis completed');
}

#[AsTask(name: 'quality:test', description: 'Run Pest tests')]
function qualityTest (
    #[AsOption(description: 'Run command inside Docker container')]
    bool $docker = false
) : void {
    io()->title('🧪 Running Pest tests...');

    if ($docker) {
        runInContainer(getDockerApp(), 'vendor/bin/pest');
    } else {
        run('./vendor/bin/pest');
    }

    io()->success('✅ Tests completed');
}

#[AsTask(name: 'quality:check', description: 'Run code analysis and linting (analyse + lint)')]
function qualityCheck (
    #[AsOption(description: 'Run commands inside Docker container')]
    bool $docker = false
) : void {
    io()->title('🔧 Quality Check: Code Analysis + Linting');

    io()->section('Running static analysis...');
    qualityAnalyse($docker);

    io()->section('Checking code style...');
    qualityLint($docker);

    io()->success('✅ Quality check completed successfully');
}

#[AsTask(name: 'quality:verify', description: 'Full quality verification (lint + analyse + test)')]
function qualityVerify (
    #[AsOption(description: 'Run commands inside Docker container')]
    bool $docker = false
) : void {
    io()->title('🚀 Full Quality Verification');

    io()->section('1/3 - Code Style Check');
    qualityLint($docker);

    io()->section('2/3 - Static Analysis');
    qualityAnalyse($docker);

    io()->section('3/3 - Tests');
    qualityTest($docker);

    io()->success('🎉 Full quality verification completed successfully');
}
