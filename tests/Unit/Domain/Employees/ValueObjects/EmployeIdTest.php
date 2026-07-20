<?php

declare(strict_types=1);

use App\Domain\Employees\ValueObjects\EmployeId;

describe('EmployeId ValueObject', function () {

    it('génère un ID unique', function () {
        $employeId = EmployeId::generate();

        expect($employeId->value)->toBeString();
        expect(strlen($employeId->value))->toBeGreaterThan(10);
    });

    it('crée depuis un string', function () {
        $uuid      = 'employe-uuid-123';
        $employeId = EmployeId::fromString($uuid);

        expect($employeId->value)->toBe($uuid);
        expect((string) $employeId)->toBe($uuid);
    });

    it('compare deux IDs identiques', function () {
        $uuid = 'employe-uuid-123';
        $id1  = EmployeId::fromString($uuid);
        $id2  = EmployeId::fromString($uuid);

        expect($id1->equals($id2))->toBeTrue();
    });

    it('compare deux IDs différents', function () {
        $id1 = EmployeId::fromString('employe-1');
        $id2 = EmployeId::fromString('employe-2');

        expect($id1->equals($id2))->toBeFalse();
    });
});
