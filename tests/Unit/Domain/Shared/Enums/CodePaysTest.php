<?php

declare(strict_types=1);

use App\Domain\Shared\Enums\CodePays;

describe('CodePays Enum', function () {

    it('has basic enum functionality', function () {
        $cases = CodePays::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
    });

    it('each case has a value', function () {
        $cases = CodePays::cases();
        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect(strlen($case->value))->toBeGreaterThan(0);
        }
    });

    it('can be converted to string', function () {
        $cases = CodePays::cases();
        foreach ($cases as $case) {
            expect((string) $case)->toBeString();
        }
    });
});
