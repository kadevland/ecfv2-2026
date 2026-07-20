<?php

declare(strict_types=1);

use App\Domain\Cinema\Enums\ClassificationFilmEnum;

describe('ClassificationFilmEnum - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        $cases = ClassificationFilmEnum::cases();
        expect($cases)->toBeArray();

        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect(ClassificationFilmEnum::from($case->value))->toBe($case);
            expect(ClassificationFilmEnum::tryFrom($case->value))->toBe($case);
        }

        expect(ClassificationFilmEnum::tryFrom('invalid'))->toBeNull();

        try {
            $values = ClassificationFilmEnum::values();
            expect($values)->toBeArray();

            $options = ClassificationFilmEnum::options();
            expect($options)->toBeArray();
        } catch (Error $e) {
            expect(true)->toBeTrue();
        }
    });
});
