<?php

declare(strict_types=1);

use App\Domain\Employees\ValueObjects\PlanningId;

describe('PlanningId ValueObject', function () {

    it('génère un ID unique', function () {
        $planningId = PlanningId::generate();

        expect($planningId->value)->toBeString();
        expect(strlen($planningId->value))->toBeGreaterThan(10);
    });

    it('crée depuis un string', function () {
        $uuid       = 'planning-uuid-123';
        $planningId = PlanningId::fromString($uuid);

        expect($planningId->value)->toBe($uuid);
        expect((string) $planningId)->toBe($uuid);
    });

    it('compare deux IDs', function () {
        $id1 = PlanningId::fromString('planning-1');
        $id2 = PlanningId::fromString('planning-2');

        expect($id1->equals($id2))->toBeFalse();
    });
});
