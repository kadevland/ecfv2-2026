<?php

declare(strict_types=1);
use App\Domain\Public\Repositories\SeancePublicRepositoryInterface;

describe('SeancePublicRepositoryInterface - Coverage', function () {
    it('interface exists and can be mocked', function () {
        expect(interface_exists(SeancePublicRepositoryInterface::class))->toBeTrue();
        $mock = \Mockery::mock(SeancePublicRepositoryInterface::class);
        expect($mock)->toBeInstanceOf(SeancePublicRepositoryInterface::class);
    });
});
