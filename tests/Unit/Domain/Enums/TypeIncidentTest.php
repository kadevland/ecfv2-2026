<?php

declare(strict_types=1);
use App\Domain\Enums\TypeIncident;

describe('TypeIncident Enum', function () {
    it('has basic enum functionality', function () {
        $cases = TypeIncident::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
    });
});
