<?php

declare(strict_types=1);
use App\Domain\Cinema\Enums\TypeSiegeEnum;

describe('TypeSiegeEnum - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        $cases = TypeSiegeEnum::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);

        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect($case->name)->toBeString();
            expect(TypeSiegeEnum::from($case->value))->toBe($case);
            expect(TypeSiegeEnum::tryFrom($case->value))->toBe($case);
        }
        expect(TypeSiegeEnum::tryFrom('invalid_value_test'))->toBeNull();
    });
});
