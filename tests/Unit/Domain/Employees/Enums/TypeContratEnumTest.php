<?php

declare(strict_types=1);
use App\Domain\Employees\Enums\TypeContratEnum;

describe('TypeContratEnum', function () {
    it('has cases', function () {
        $cases = TypeContratEnum::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect($case->name)->toBeString();
        }
    });
    it('can use from and tryFrom', function () {
        $first = TypeContratEnum::cases()[0] ?? null;
        if ($first) {
            expect(TypeContratEnum::from($first->value))->toBe($first);
            expect(TypeContratEnum::tryFrom($first->value))->toBe($first);
        }
        expect(TypeContratEnum::tryFrom('invalid'))->toBeNull();
    });
});
