<?php

declare(strict_types=1);

use App\Domain\Enums\StatutBillet;

describe('StatutBillet Enum', function () {

    it('has basic enum functionality', function () {
        $cases = StatutBillet::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
    });

    it('each case has a value', function () {
        $cases = StatutBillet::cases();
        foreach ($cases as $case) {
            expect($case->value)->toBeString();
        }
    });
});
