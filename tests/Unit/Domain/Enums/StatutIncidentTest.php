<?php

declare(strict_types=1);

use App\Domain\Enums\StatutIncident;

describe('StatutIncident Enum', function () {

    it('has basic enum functionality', function () {
        $cases = StatutIncident::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
    });

    it('each case has a value', function () {
        $cases = StatutIncident::cases();
        foreach ($cases as $case) {
            expect($case->value)->toBeString();
        }
    });
});
