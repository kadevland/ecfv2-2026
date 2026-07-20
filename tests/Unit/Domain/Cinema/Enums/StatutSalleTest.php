<?php

declare(strict_types=1);

use App\Domain\Cinema\Enums\StatutSalle;

describe('StatutSalle Enum', function () {

    it('has basic enum functionality', function () {
        $cases = StatutSalle::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
    });

    it('can get enum values', function () {
        $cases = StatutSalle::cases();
        foreach ($cases as $case) {
            expect($case->value)->toBeString();
        }
    });
});
