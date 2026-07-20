<?php

declare(strict_types=1);

use App\Domain\Employees\ValueObjects\IncidentId;

describe('IncidentId ValueObject', function () {

    it('génère un ID unique', function () {
        $incidentId = IncidentId::generate();

        expect($incidentId->value)->toBeString();
        expect(strlen($incidentId->value))->toBeGreaterThan(10);
    });

    it('crée depuis un string', function () {
        $uuid       = 'incident-uuid-123';
        $incidentId = IncidentId::fromString($uuid);

        expect($incidentId->value)->toBe($uuid);
        expect((string) $incidentId)->toBe($uuid);
    });

    it('compare deux IDs identiques', function () {
        $uuid = 'incident-uuid-123';
        $id1  = IncidentId::fromString($uuid);
        $id2  = IncidentId::fromString($uuid);

        expect($id1->equals($id2))->toBeTrue();
    });

    it('compare deux IDs différents', function () {
        $id1 = IncidentId::fromString('incident-1');
        $id2 = IncidentId::fromString('incident-2');

        expect($id1->equals($id2))->toBeFalse();
    });
});
