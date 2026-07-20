<?php

declare(strict_types=1);

use App\Domain\Enums\StatutPaiement;

describe('StatutPaiement Enum', function () {

    it('has basic enum functionality', function () {
        $cases = StatutPaiement::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
    });

    it('each case has a value', function () {
        $cases = StatutPaiement::cases();
        foreach ($cases as $case) {
            expect($case->value)->toBeString();
        }
    });
});
