<?php

declare(strict_types=1);
use App\Domain\Shared\Enums\ThemeUIEnum;

describe('ThemeUIEnum - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        // Test basic enum functionality
        $cases = ThemeUIEnum::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBe(3);

        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect($case->name)->toBeString();
            expect(ThemeUIEnum::from($case->value))->toBe($case);
            expect(ThemeUIEnum::tryFrom($case->value))->toBe($case);
        }

        expect(ThemeUIEnum::tryFrom('invalid_test'))->toBeNull();

        // Test custom methods
        expect(ThemeUIEnum::default())->toBe(ThemeUIEnum::AUTO);

        $values = ThemeUIEnum::values();
        expect($values)->toBeArray();
        expect($values)->toContain('auto', 'light', 'dark');

        $options = ThemeUIEnum::options();
        expect($options)->toBeArray();
        expect(array_keys($options))->toBe(['auto', 'light', 'dark']);

        // Test all cases labels
        expect(ThemeUIEnum::AUTO->label())->toBe('Automatique');
        expect(ThemeUIEnum::LIGHT->label())->toBe('Clair');
        expect(ThemeUIEnum::DARK->label())->toBe('Sombre');

        // Test all cases icons
        expect(ThemeUIEnum::AUTO->icon())->toBe('🔄');
        expect(ThemeUIEnum::LIGHT->icon())->toBe('☀️');
        expect(ThemeUIEnum::DARK->icon())->toBe('🌙');

        // Test all cases CSS classes
        expect(ThemeUIEnum::AUTO->cssClass())->toBe('theme-auto');
        expect(ThemeUIEnum::LIGHT->cssClass())->toBe('theme-light');
        expect(ThemeUIEnum::DARK->cssClass())->toBe('theme-dark');
    });
});
