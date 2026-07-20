<?php

declare(strict_types=1);
use App\Domain\Enums\UserType;

describe('UserType - Coverage 100%', function () {
    it('complete coverage', function () {
        foreach (UserType::cases() as $case) {
            expect($case->value)->toBeString();
            expect(UserType::from($case->value))->toBe($case);
        }
        expect(UserType::tryFrom('invalid'))->toBeNull();
    });
});
