<?php

declare(strict_types=1);
use App\Domain\Enums\NiveauAcces;

describe('NiveauAcces Enum', function () {
    it('has cases', function () {
        expect(NiveauAcces::cases())->toBeArray();
    });
});
