<?php

declare(strict_types=1);

use App\Domain\Cinema\Enums\QualiteSonore;

describe('QualiteSonore Enum', function () {

    it('has basic enum functionality', function () {
        $cases = QualiteSonore::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
    });

    it('each case has a value', function () {
        $cases = QualiteSonore::cases();
        foreach ($cases as $case) {
            expect($case->value)->toBeString();
        }
    });

    it('can be used in arrays', function () {
        $cases = QualiteSonore::cases();
        $array = [$cases[0] ?? null];
        expect($array)->toBeArray();
        expect(count($array))->toBe(1);
    });
});
