<?php

declare(strict_types=1);

use App\Domain\Enums\Poste;

describe('Poste Enum', function () {

    it('has basic enum functionality', function () {
        $cases = Poste::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
    });

    it('each case has a value', function () {
        $cases = Poste::cases();
        foreach ($cases as $case) {
            expect($case->value)->toBeString();
        }
    });
});
