<?php

declare(strict_types=1);
use App\Domain\Cinema\Enums\VersionFilmEnum;

describe('VersionFilmEnum - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        $cases = VersionFilmEnum::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBe(3);

        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect($case->name)->toBeString();
            expect(VersionFilmEnum::from($case->value))->toBe($case);
            expect(VersionFilmEnum::tryFrom($case->value))->toBe($case);
        }
        expect(VersionFilmEnum::tryFrom('invalid_test'))->toBeNull();

        // Test custom methods
        $values = VersionFilmEnum::values();
        expect($values)->toBeArray();
        expect($values)->toContain('vf', 'vo', 'vostfr');

        $options = VersionFilmEnum::options();
        expect($options)->toBeArray();
        expect(array_keys($options))->toBe(['vf', 'vo', 'vostfr']);

        // Test all cases labels
        expect(VersionFilmEnum::VF->label())->toBe('Version française');
        expect(VersionFilmEnum::VO->label())->toBe('Version originale');
        expect(VersionFilmEnum::VOSTFR->label())->toBe('Version originale sous-titrée français');

        // Test all cases short labels
        expect(VersionFilmEnum::VF->shortLabel())->toBe('VF');
        expect(VersionFilmEnum::VO->shortLabel())->toBe('VO');
        expect(VersionFilmEnum::VOSTFR->shortLabel())->toBe('VOSTFR');
    });
});
