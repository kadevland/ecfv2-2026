<?php

declare(strict_types=1);
use App\Domain\Enums\SeveriteIncident;

describe('SeveriteIncident Enum', function () {
    it('has cases', function () {
        expect(SeveriteIncident::cases())->toBeArray();
    });
});
