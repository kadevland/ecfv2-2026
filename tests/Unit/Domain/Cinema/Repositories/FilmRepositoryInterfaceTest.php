<?php

declare(strict_types=1);
use App\Domain\Cinema\Repositories\FilmRepositoryInterface;

describe('FilmRepositoryInterface', function () {
    it('interface exists and can be mocked', function () {
        expect(interface_exists(FilmRepositoryInterface::class))->toBeTrue();
        $mock = \Mockery::mock(FilmRepositoryInterface::class);
        expect($mock)->toBeInstanceOf(FilmRepositoryInterface::class);
    });
});
