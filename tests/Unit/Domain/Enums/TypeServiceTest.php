<?php

declare(strict_types=1);
use App\Domain\Enums\TypeService;

describe('TypeService - Coverage 100%', function () {
    it('complete coverage', function () {
        foreach (TypeService::cases() as $case) {
            expect($case->value)->toBeString();
            expect(TypeService::from($case->value))->toBe($case);
        }
        expect(TypeService::tryFrom('invalid'))->toBeNull();
    });
});
