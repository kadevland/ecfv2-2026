<?php

declare(strict_types=1);
use App\Domain\Employees\Repositories\IncidentRepositoryInterface;

describe('IncidentRepositoryInterface', function () {
    it('interface exists and can be mocked', function () {
        expect(interface_exists(IncidentRepositoryInterface::class))->toBeTrue();
        $mock = \Mockery::mock(IncidentRepositoryInterface::class);
        expect($mock)->toBeInstanceOf(IncidentRepositoryInterface::class);
    });
});
