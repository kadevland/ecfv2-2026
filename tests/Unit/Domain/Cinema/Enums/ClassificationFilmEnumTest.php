<?php

declare(strict_types=1);
use App\Domain\Cinema\Enums\ClassificationFilmEnum;

describe('ClassificationFilmEnum', function () {
    it('complete coverage', function () {
        foreach (ClassificationFilmEnum::cases() as $case) {
            expect($case->value)->toBeString();
            expect(ClassificationFilmEnum::from($case->value))->toBe($case);
        }
        expect(ClassificationFilmEnum::tryFrom('invalid'))->toBeNull();
    });
});
