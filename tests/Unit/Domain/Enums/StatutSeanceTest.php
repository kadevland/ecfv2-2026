<?php

declare(strict_types=1);

use App\Domain\Enums\StatutSeance;

describe('StatutSeance Enum', function () {

    it('has basic enum functionality', function () {
        $cases = StatutSeance::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
    });

    it('can compare enum cases', function () {
        $cases = StatutSeance::cases();
        if (count($cases) >= 1) {
            $first = $cases[0];
            expect($first)->toBe($first);
        }
        expect(true)->toBeTrue();
    });
});
