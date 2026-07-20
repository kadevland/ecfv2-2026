<?php

declare(strict_types=1);

use App\Domain\Cinema\Enums\QualiteProjection;

describe('QualiteProjection Enum - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        // Test basic enum functionality
        $cases = QualiteProjection::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBe(6);

        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect($case->name)->toBeString();
            expect(QualiteProjection::from($case->value))->toBe($case);
            expect(QualiteProjection::tryFrom($case->value))->toBe($case);
        }

        expect(QualiteProjection::tryFrom('invalid'))->toBeNull();

        // Test custom methods
        $values = QualiteProjection::getValues();
        expect($values)->toBeArray();
        expect($values)->toContain('2K', '4K', 'IMAX', 'DOLBY_VISION', 'LASER', 'HDR');

        $options = QualiteProjection::getOptions();
        expect($options)->toBeArray();
        expect(array_keys($options))->toBe(['2K', '4K', 'IMAX', 'DOLBY_VISION', 'LASER', 'HDR']);

        // Test all cases labels
        expect(QualiteProjection::NUMERIQUE_2K->getLabel())->toBe('2K Numérique');
        expect(QualiteProjection::NUMERIQUE_4K->getLabel())->toBe('4K Ultra HD');
        expect(QualiteProjection::IMAX->getLabel())->toBe('IMAX');
        expect(QualiteProjection::DOLBY_VISION->getLabel())->toBe('Dolby Vision');
        expect(QualiteProjection::LASER->getLabel())->toBe('Projection Laser');
        expect(QualiteProjection::HDR->getLabel())->toBe('HDR');
    });
});
