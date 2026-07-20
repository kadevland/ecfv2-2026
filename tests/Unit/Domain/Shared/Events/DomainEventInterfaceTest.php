<?php

declare(strict_types=1);
use App\Domain\Shared\Events\DomainEventInterface;

describe('DomainEventInterface - Coverage', function () {
    it('interface exists', function () {
        expect(interface_exists(DomainEventInterface::class))->toBeTrue();
    });
    it('can create mock', function () {
        $mock = \Mockery::mock(DomainEventInterface::class);
        expect($mock)->toBeInstanceOf(DomainEventInterface::class);
    });
});
