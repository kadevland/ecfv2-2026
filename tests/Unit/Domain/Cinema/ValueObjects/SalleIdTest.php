<?php

declare(strict_types=1);

use App\Domain\Cinema\ValueObjects\SalleId;

describe('SalleId ValueObject', function () {

    it('génère un ID unique', function () {
        $salleId = SalleId::generate();

        expect($salleId->value)->toBeString();
        expect(strlen($salleId->value))->toBeGreaterThan(10);
    });

    it('crée depuis un string', function () {
        $uuid    = 'a1b2c3d4-e5f6-4321-8765-123456789abc';
        $salleId = SalleId::fromString($uuid);

        expect($salleId->value)->toBe($uuid);
        expect((string) $salleId)->toBe($uuid);
    });

    it('compare deux IDs identiques', function () {
        $uuid = 'a1b2c3d4-e5f6-4321-8765-123456789abc';
        $id1  = SalleId::fromString($uuid);
        $id2  = SalleId::fromString($uuid);

        expect($id1->equals($id2))->toBeTrue();
    });

    it('compare deux IDs différents', function () {
        $id1 = SalleId::fromString('a1b2c3d4-e5f6-4321-8765-111111111111');
        $id2 = SalleId::fromString('a2b2c3d4-e5f6-4321-8765-222222222222');

        expect($id1->equals($id2))->toBeFalse();
    });
});
