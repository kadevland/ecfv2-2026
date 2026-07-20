<?php

declare(strict_types=1);
use App\Domain\Reviews\Enums\ReviewStatusEnum;

describe('ReviewStatusEnum - Coverage 100%', function () {
    it('complete coverage', function () {
        foreach (ReviewStatusEnum::cases() as $case) {
            expect($case->value)->toBeString();
            expect(ReviewStatusEnum::from($case->value))->toBe($case);
        }
        expect(ReviewStatusEnum::tryFrom('invalid'))->toBeNull();
    });
});
