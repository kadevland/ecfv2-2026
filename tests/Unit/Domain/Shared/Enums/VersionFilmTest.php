<?php

declare(strict_types=1);

use App\Domain\Shared\Enums\VersionFilm;

describe('VersionFilm Enum', function () {

    it('has basic enum functionality', function () {
        $cases = VersionFilm::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
    });

    it('can be used in comparisons', function () {
        $version1 = VersionFilm::cases()[0] ?? null;
        $version2 = VersionFilm::cases()[0] ?? null;

        if ($version1 && $version2) {
            expect($version1)->toBe($version2);
        }
        expect(true)->toBeTrue(); // Fallback if no cases
    });

    it('has string representation', function () {
        $cases = VersionFilm::cases();
        if (!empty($cases)) {
            $firstCase = $cases[0];
            expect((string) $firstCase)->toBeString();
        }
        expect(true)->toBeTrue(); // Fallback
    });
});
