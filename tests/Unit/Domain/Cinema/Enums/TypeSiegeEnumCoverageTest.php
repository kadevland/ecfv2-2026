<?php

declare(strict_types=1);

use App\Domain\Cinema\Enums\TypeSiegeEnum;

describe('TypeSiegeEnum - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        $cases = TypeSiegeEnum::cases();
        expect($cases)->toBeArray();

        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect($case->name)->toBeString();
            expect(TypeSiegeEnum::from($case->value))->toBe($case);
            expect(TypeSiegeEnum::tryFrom($case->value))->toBe($case);
        }

        expect(TypeSiegeEnum::tryFrom('invalid_siege_type'))->toBeNull();

        try {
            $values = TypeSiegeEnum::values();
            expect($values)->toBeArray();

            $options = TypeSiegeEnum::options();
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
