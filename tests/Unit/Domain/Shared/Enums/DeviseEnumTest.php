<?php

declare(strict_types=1);

use App\Domain\Shared\Enums\DeviseEnum;

describe('DeviseEnum - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        // Test basic enum functionality
        $cases = DeviseEnum::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBe(4);

        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect(DeviseEnum::from($case->value))->toBe($case);
            expect(DeviseEnum::tryFrom($case->value))->toBe($case);
        }

        expect(DeviseEnum::tryFrom('invalid'))->toBeNull();

        // Test custom methods
        expect(DeviseEnum::default())->toBe(DeviseEnum::EUR);

        $values = DeviseEnum::values();
        expect($values)->toBeArray();
        expect($values)->toContain('EUR', 'CHF', 'CAD', 'USD');

        $options = DeviseEnum::options();
        expect($options)->toBeArray();
        expect(array_keys($options))->toBe(['EUR', 'CHF', 'CAD', 'USD']);

        // Test all cases symbols
        expect(DeviseEnum::EUR->symbol())->toBe('€');
        expect(DeviseEnum::CHF->symbol())->toBe('CHF');
        expect(DeviseEnum::CAD->symbol())->toBe('CA$');
        expect(DeviseEnum::USD->symbol())->toBe('$');

        // Test all cases labels
        expect(DeviseEnum::EUR->label())->toBe('Euro');
        expect(DeviseEnum::CHF->label())->toBe('Franc suisse');
        expect(DeviseEnum::CAD->label())->toBe('Dollar canadien');
        expect(DeviseEnum::USD->label())->toBe('Dollar américain');

        // Test all cases decimals
        expect(DeviseEnum::EUR->decimals())->toBe(2);
        expect(DeviseEnum::CHF->decimals())->toBe(2);
        expect(DeviseEnum::CAD->decimals())->toBe(2);
        expect(DeviseEnum::USD->decimals())->toBe(2);
    });
});
