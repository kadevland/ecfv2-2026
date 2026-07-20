<?php

declare(strict_types=1);
use App\Domain\Enums\TempsTravail;

describe('TempsTravail - Coverage 100%', function () {
    it('complete coverage', function () {
        foreach (TempsTravail::cases() as $case) {
            expect($case->value)->toBeString();
            expect(TempsTravail::from($case->value))->toBe($case);
        }
        expect(TempsTravail::tryFrom('invalid'))->toBeNull();
    });
});
