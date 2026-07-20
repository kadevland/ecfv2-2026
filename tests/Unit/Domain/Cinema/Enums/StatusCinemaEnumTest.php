<?php

declare(strict_types=1);
use App\Domain\Cinema\Enums\StatusCinemaEnum;

describe('StatusCinemaEnum', function () {
    it('complete coverage', function () {
        $cases = StatusCinemaEnum::cases();
        expect($cases)->toBeArray();
        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect($case->name)->toBeString();
            expect(StatusCinemaEnum::from($case->value))->toBe($case);
            expect(StatusCinemaEnum::tryFrom($case->value))->toBe($case);
        }
        expect(StatusCinemaEnum::tryFrom('invalid'))->toBeNull();
    });
});
