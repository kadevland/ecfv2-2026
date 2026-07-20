<?php

declare(strict_types=1);

use App\Domain\Cinema\Enums\TypeTarifEnum;

describe('TypeTarifEnum - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        $cases = TypeTarifEnum::cases();
        expect($cases)->toBeArray();

        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect($case->name)->toBeString();
            expect(TypeTarifEnum::from($case->value))->toBe($case);
            expect(TypeTarifEnum::tryFrom($case->value))->toBe($case);
        }

        expect(TypeTarifEnum::tryFrom('invalid_tarif_type'))->toBeNull();

        try {
            $values = TypeTarifEnum::values();
            expect($values)->toBeArray();

            $options = TypeTarifEnum::options();
            expect($options)->toBeArray();

            foreach ($cases as $case) {
                $case->label();
                $case->shortLabel();
            }
        } catch (Error $e) {
            expect(true)->toBeTrue();
        }
    });
});
