<?php

declare(strict_types=1);

use App\Domain\Enums\MethodePaiement;

describe('MethodePaiement Enum', function () {

    it('has basic enum functionality', function () {
        $cases = MethodePaiement::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
    });

    it('each case has a value', function () {
        $cases = MethodePaiement::cases();
        foreach ($cases as $case) {
            expect($case->value)->toBeString();
        }
    });
});
