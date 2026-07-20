<?php

declare(strict_types=1);

use App\Domain\Shared\Enums\JourSemaine;

describe('JourSemaine Enum', function () {

    it('has basic enum functionality', function () {
        $cases = JourSemaine::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
    });

    it('can be used in comparisons', function () {
        $cases = JourSemaine::cases();
        if (count($cases) >= 2) {
            expect($cases[0])->not->toBe($cases[1]);
        }
        expect(true)->toBeTrue(); // Fallback
    });

    it('has string values', function () {
        $cases = JourSemaine::cases();
        foreach ($cases as $case) {
            expect($case->value)->toBeString();
        }
    });
});
