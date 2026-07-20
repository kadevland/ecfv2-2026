<?php

declare(strict_types=1);

use App\Domain\Cinema\Enums\QualiteSonore;

describe('QualiteSonore Enum - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        $cases = QualiteSonore::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBe(5);

        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect(QualiteSonore::from($case->value))->toBe($case);
            expect(QualiteSonore::tryFrom($case->value))->toBe($case);
        }

        expect(QualiteSonore::tryFrom('invalid'))->toBeNull();

        // Test custom methods
        $values = QualiteSonore::getValues();
        expect($values)->toBeArray();
        expect($values)->toContain('DOLBY_SURROUND', 'DOLBY_ATMOS', 'DTS', 'DTS_X', 'IMAX_ENHANCED');

        $options = QualiteSonore::getOptions();
        expect($options)->toBeArray();

        // Test all labels
        expect(QualiteSonore::DOLBY_SURROUND->getLabel())->toBe('Dolby Surround');
        expect(QualiteSonore::DOLBY_ATMOS->getLabel())->toBe('Dolby Atmos');
        expect(QualiteSonore::DTS->getLabel())->toBe('DTS');
        expect(QualiteSonore::DTS_X->getLabel())->toBe('DTS:X');
        expect(QualiteSonore::IMAX_ENHANCED->getLabel())->toBe('IMAX Enhanced');
    });
});
