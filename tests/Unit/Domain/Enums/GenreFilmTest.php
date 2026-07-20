<?php

declare(strict_types=1);

use App\Domain\Enums\GenreFilm;

describe('GenreFilm Enum', function () {

    it('has basic enum functionality', function () {
        $cases = GenreFilm::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
    });

    it('each case has a value', function () {
        $cases = GenreFilm::cases();
        foreach ($cases as $case) {
            expect($case->value)->toBeString();
        }
    });
});
