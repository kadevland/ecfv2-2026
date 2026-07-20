<?php

declare(strict_types=1);

use App\Domain\Shared\ValueObjects\Identity;

describe('Identity ValueObject', function () {

    it('can create identity', function () {
        $identity = Identity::fromString('test-id-123');

        expect($identity->value)->toBe('test-id-123');
        expect((string) $identity)->toBe('test-id-123');
    });

    it('can generate unique identity', function () {
        $identity = Identity::generate();

        expect($identity->value)->toBeString();
        expect(strlen($identity->value))->toBeGreaterThan(10);
    });

    it('can compare identities', function () {
        $id1 = Identity::fromString('same-id');
        $id2 = Identity::fromString('same-id');

        expect($id1->equals($id2))->toBeTrue();
    });
});
