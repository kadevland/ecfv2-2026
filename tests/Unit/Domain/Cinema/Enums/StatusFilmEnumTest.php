<?php

declare(strict_types=1);
use App\Domain\Cinema\Enums\StatusFilmEnum;

describe('StatusFilmEnum', function () {
    it('complete coverage', function () {
        foreach (StatusFilmEnum::cases() as $case) {
            expect($case->value)->toBeString();
            expect(StatusFilmEnum::from($case->value))->toBe($case);
        }
        expect(StatusFilmEnum::tryFrom('invalid'))->toBeNull();
    });
});
