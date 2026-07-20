<?php

declare(strict_types=1);

test('basic pest test works in docker', function () {
    expect(true)->toBeTrue();
});

test('laravel environment is loaded', function () {
    expect(config('app.name'))->toBe('Cinéphoria Tests');
});

test('basic math works', function () {
    expect(2 + 2)->toBe(4);
});
