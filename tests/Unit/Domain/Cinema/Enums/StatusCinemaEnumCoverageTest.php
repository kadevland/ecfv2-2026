<?php

declare(strict_types=1);

use App\Domain\Cinema\Enums\StatusCinemaEnum;

describe('StatusCinemaEnum - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        $cases = StatusCinemaEnum::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBe(3);

        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect(StatusCinemaEnum::from($case->value))->toBe($case);
            expect(StatusCinemaEnum::tryFrom($case->value))->toBe($case);
        }

        expect(StatusCinemaEnum::tryFrom('invalid'))->toBeNull();

        // Test custom methods
        $values = StatusCinemaEnum::values();
        expect($values)->toBeArray();
        expect($values)->toContain('actif', 'inactif', 'maintenance');

        $options = StatusCinemaEnum::options();
        expect($options)->toBeArray();
        expect(array_keys($options))->toBe(['actif', 'inactif', 'maintenance']);

        // Test individual cases
        expect(StatusCinemaEnum::ACTIF->value)->toBe('actif');
        expect(StatusCinemaEnum::INACTIF->value)->toBe('inactif');
        expect(StatusCinemaEnum::MAINTENANCE->value)->toBe('maintenance');
    });
});
