<?php

declare(strict_types=1);
use App\Domain\Users\Repositories\UserRepositoryInterface;

describe('UserRepositoryInterface - Coverage', function () {
    it('interface exists and can be mocked', function () {
        expect(interface_exists(UserRepositoryInterface::class))->toBeTrue();
        $mock = \Mockery::mock(UserRepositoryInterface::class);
        expect($mock)->toBeInstanceOf(UserRepositoryInterface::class);
    });
});
