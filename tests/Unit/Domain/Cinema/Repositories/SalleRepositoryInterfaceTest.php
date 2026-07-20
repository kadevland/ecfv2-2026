<?php

declare(strict_types=1);
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;

describe('SalleRepositoryInterface', function () {
    it('interface exists', function () {
        expect(interface_exists(SalleRepositoryInterface::class))->toBeTrue();
    });
    it('can create mock', function () {
        $mock = \Mockery::mock(SalleRepositoryInterface::class);
        expect($mock)->toBeInstanceOf(SalleRepositoryInterface::class);
    });
});
