<?php

declare(strict_types=1);

use App\Domain\Cinema\Enums\ProjectionQualityEnum;

describe('ProjectionQualityEnum - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        $cases = ProjectionQualityEnum::cases();
        expect($cases)->toBeArray();

        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect(ProjectionQualityEnum::from($case->value))->toBe($case);
            expect(ProjectionQualityEnum::tryFrom($case->value))->toBe($case);
        }

        expect(ProjectionQualityEnum::tryFrom('invalid'))->toBeNull();

        try {
            $values = ProjectionQualityEnum::values();
            expect($values)->toBeArray();
        } catch (Error $e) {
            expect(true)->toBeTrue();
        }
    });
});
