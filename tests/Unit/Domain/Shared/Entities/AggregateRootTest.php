<?php

declare(strict_types=1);
use App\Domain\Shared\Entities\AggregateRoot;

describe('AggregateRoot - Coverage', function () {
    it('class exists', function () {
        expect(class_exists(AggregateRoot::class))->toBeTrue();
    });
    it('can create mock', function () {
        $mock = \Mockery::mock(AggregateRoot::class);
        expect($mock)->toBeInstanceOf(AggregateRoot::class);
    });
});
