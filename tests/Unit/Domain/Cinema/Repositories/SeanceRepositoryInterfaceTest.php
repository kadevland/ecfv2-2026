<?php

declare(strict_types=1);
use App\Domain\Cinema\Repositories\SeanceRepositoryInterface;

describe('SeanceRepositoryInterface', function () {
    it('interface exists and can be mocked', function () {
        expect(interface_exists(SeanceRepositoryInterface::class))->toBeTrue();
        $mock = \Mockery::mock(SeanceRepositoryInterface::class);
        expect($mock)->toBeInstanceOf(SeanceRepositoryInterface::class);
    });
});
