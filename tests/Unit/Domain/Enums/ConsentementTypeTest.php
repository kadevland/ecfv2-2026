<?php

declare(strict_types=1);
use App\Domain\Enums\ConsentementType;

describe('ConsentementType - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        foreach (ConsentementType::cases() as $case) {
            expect($case->value)->toBeString();
            expect(ConsentementType::from($case->value))->toBe($case);
        }
        expect(ConsentementType::tryFrom('invalid'))->toBeNull();
    });
});
