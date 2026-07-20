<?php

declare(strict_types=1);
use App\Domain\Shared\Enums\SexeEnum;

describe('SexeEnum', function () {
    it('has cases', function () {
        $cases = SexeEnum::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect($case->name)->toBeString();
        }
    });
    it('can use from', function () {
        $first = SexeEnum::cases()[0] ?? null;
        if ($first) {
            expect(SexeEnum::from($first->value))->toBe($first);
        }
        expect(true)->toBeTrue();
    });
    it('can use tryFrom', function () {
        expect(SexeEnum::tryFrom('invalid'))->toBeNull();
        $first = SexeEnum::cases()[0] ?? null;
        if ($first) {
            expect(SexeEnum::tryFrom($first->value))->toBe($first);
        }
    });
});
