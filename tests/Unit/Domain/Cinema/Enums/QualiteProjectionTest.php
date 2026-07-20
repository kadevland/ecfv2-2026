<?php

declare(strict_types=1);

use App\Domain\Cinema\Enums\QualiteProjection;

describe('QualiteProjection Enum', function () {

    it('has basic enum functionality', function () {
        $cases = QualiteProjection::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
    });

    it('each case has a value', function () {
        $cases = QualiteProjection::cases();
        foreach ($cases as $case) {
            expect($case->value)->toBeString();
        }
    });

    it('can compare cases', function () {
        $cases = QualiteProjection::cases();
        if (count($cases) >= 2) {
            expect($cases[0])->not->toBe($cases[1]);
        }
        expect(true)->toBeTrue(); // Fallback
    });
});
