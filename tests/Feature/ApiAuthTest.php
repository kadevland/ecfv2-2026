<?php

declare(strict_types=1);

test('api guard is registered', function () {
    $guards = config('auth.guards');

    expect($guards)->toHaveKey('api');
    expect($guards['api']['driver'])->toBe('sanctum');
    expect($guards['api']['provider'])->toBe('users');
});

test('api uses same provider as web', function () {
    $guards = config('auth.guards');

    expect($guards['api']['provider'])->toBe('users');
    expect($guards['web']['provider'])->toBe('users');
});
