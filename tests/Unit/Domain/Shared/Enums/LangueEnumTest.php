<?php

declare(strict_types=1);
use App\Domain\Shared\Enums\LangueEnum;

describe('LangueEnum', function () {
    it('has cases', function () {
        $cases = LangueEnum::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
    });
    it('can access values', function () {
        foreach (LangueEnum::cases() as $case) {
            expect($case->value)->toBeString();
            expect($case->name)->toBeString();
        }
    });
    it('can use from', function () {
        $first = LangueEnum::cases()[0] ?? null;
        if ($first) {
            expect(LangueEnum::from($first->value))->toBe($first);
        }
        expect(true)->toBeTrue();
    });
    it('can use tryFrom', function () {
        expect(LangueEnum::tryFrom('invalid'))->toBeNull();
    });
});
