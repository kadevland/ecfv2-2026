<?php

declare(strict_types=1);
use App\Domain\Shared\Events\DomainEvent;

describe('DomainEvent - Coverage', function () {
    it('class exists', function () {
        expect(class_exists(DomainEvent::class))->toBeTrue();
    });
    it('can create mock', function () {
        $mock = \Mockery::mock(DomainEvent::class);
        expect($mock)->toBeInstanceOf(DomainEvent::class);
    });
});
