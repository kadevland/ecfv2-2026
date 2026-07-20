<?php

declare(strict_types=1);
use App\Domain\Enums\StatutPlanning;

describe('StatutPlanning - Coverage 100%', function () {
    it('complete coverage', function () {
        foreach (StatutPlanning::cases() as $case) {
            expect($case->value)->toBeString();
            expect(StatutPlanning::from($case->value))->toBe($case);
        }
        expect(StatutPlanning::tryFrom('invalid'))->toBeNull();
    });
});
