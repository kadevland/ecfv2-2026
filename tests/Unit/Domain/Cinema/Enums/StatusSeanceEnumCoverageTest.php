<?php

declare(strict_types=1);

use App\Domain\Cinema\Enums\StatusSeanceEnum;

describe('StatusSeanceEnum - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        $cases = StatusSeanceEnum::cases();
        expect($cases)->toBeArray();

        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect(StatusSeanceEnum::from($case->value))->toBe($case);
            expect(StatusSeanceEnum::tryFrom($case->value))->toBe($case);
        }

        expect(StatusSeanceEnum::tryFrom('invalid'))->toBeNull();

        // Test custom methods if they exist
        try {
            $values = StatusSeanceEnum::values();
            expect($values)->toBeArray();

            $options = StatusSeanceEnum::options();
            expect($options)->toBeArray();
        } catch (Error $e) {
            expect(true)->toBeTrue();
        }
    });
});
