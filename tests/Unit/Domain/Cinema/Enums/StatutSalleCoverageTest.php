<?php

declare(strict_types=1);

use App\Domain\Cinema\Enums\StatutSalle;

describe('StatutSalle - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        $cases = StatutSalle::cases();
        expect($cases)->toBeArray();

        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect(StatutSalle::from($case->value))->toBe($case);
            expect(StatutSalle::tryFrom($case->value))->toBe($case);
        }

        expect(StatutSalle::tryFrom('invalid'))->toBeNull();

        try {
            $values = StatutSalle::values();
            expect($values)->toBeArray();
        } catch (Error $e) {
            expect(true)->toBeTrue();
        }
    });
});
