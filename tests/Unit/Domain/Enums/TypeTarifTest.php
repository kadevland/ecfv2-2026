<?php

declare(strict_types=1);

use App\Domain\Enums\TypeTarif;

describe('TypeTarif Enum', function () {

    it('has basic enum functionality', function () {
        $cases = TypeTarif::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
    });

    it('each case has a value', function () {
        $cases = TypeTarif::cases();
        foreach ($cases as $case) {
            expect($case->value)->toBeString();
        }
    });
});
