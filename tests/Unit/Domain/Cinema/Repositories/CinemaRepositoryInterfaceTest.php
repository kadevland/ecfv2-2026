<?php

declare(strict_types=1);
use App\Domain\Cinema\Repositories\CinemaRepositoryInterface;

describe('CinemaRepositoryInterface', function () {
    it('interface exists and can be mocked', function () {
        expect(interface_exists(CinemaRepositoryInterface::class))->toBeTrue();
        $mock = \Mockery::mock(CinemaRepositoryInterface::class);
        expect($mock)->toBeInstanceOf(CinemaRepositoryInterface::class);
    });
});
