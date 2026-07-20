<?php

declare(strict_types=1);
use App\Domain\Cinema\Enums\StatusSeanceEnum;

describe('StatusSeanceEnum', function () {
    it('complete coverage', function () {
        $cases = StatusSeanceEnum::cases();
        expect($cases)->toBeArray();
        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect($case->name)->toBeString();
            expect(StatusSeanceEnum::from($case->value))->toBe($case);
            expect(StatusSeanceEnum::tryFrom($case->value))->toBe($case);
        }
        expect(StatusSeanceEnum::tryFrom('invalid'))->toBeNull();
    });
});
