<?php

declare(strict_types=1);
use App\Domain\Cinema\Enums\ProjectionQualityEnum;

describe('ProjectionQualityEnum', function () {
    it('complete coverage', function () {
        foreach (ProjectionQualityEnum::cases() as $case) {
            expect($case->value)->toBeString();
            expect(ProjectionQualityEnum::from($case->value))->toBe($case);
        }
        expect(ProjectionQualityEnum::tryFrom('invalid'))->toBeNull();
    });
});
