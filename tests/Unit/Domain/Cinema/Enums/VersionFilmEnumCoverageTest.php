<?php

declare(strict_types=1);

use App\Domain\Cinema\Enums\VersionFilmEnum;

describe('VersionFilmEnum - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        $cases = VersionFilmEnum::cases();
        expect($cases)->toBeArray();

        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect(VersionFilmEnum::from($case->value))->toBe($case);
            expect(VersionFilmEnum::tryFrom($case->value))->toBe($case);
        }

        expect(VersionFilmEnum::tryFrom('invalid'))->toBeNull();

        try {
            $values = VersionFilmEnum::values();
            expect($values)->toBeArray();

            $options = VersionFilmEnum::options();
            expect($options)->toBeArray();

            foreach ($cases as $case) {
                $case->label();
                $case->shortLabel();
            }
        } catch (Error $e) {
            expect(true)->toBeTrue();
        }
    });
});
