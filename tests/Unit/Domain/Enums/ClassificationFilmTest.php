<?php

declare(strict_types=1);
use App\Domain\Enums\ClassificationFilm;

describe('ClassificationFilm Enum', function () {
    it('has cases', function () {
        expect(ClassificationFilm::cases())->toBeArray();
    });
});
