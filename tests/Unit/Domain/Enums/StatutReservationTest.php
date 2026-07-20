<?php

declare(strict_types=1);

use App\Domain\Enums\StatutReservation;

describe('StatutReservation Enum', function () {

    it('has basic enum functionality', function () {
        $cases = StatutReservation::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
    });

    it('each case has a value', function () {
        $cases = StatutReservation::cases();
        foreach ($cases as $case) {
            expect($case->value)->toBeString();
        }
    });
});
