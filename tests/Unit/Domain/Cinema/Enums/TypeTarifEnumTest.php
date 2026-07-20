<?php

declare(strict_types=1);
use App\Domain\Cinema\Enums\TypeTarifEnum;

describe('TypeTarifEnum - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        $cases = TypeTarifEnum::cases();
        expect($cases)->toBeArray();
        expect(count($cases))->toBe(6);

        foreach ($cases as $case) {
            expect($case->value)->toBeString();
            expect($case->name)->toBeString();
            expect(TypeTarifEnum::from($case->value))->toBe($case);
            expect(TypeTarifEnum::tryFrom($case->value))->toBe($case);
        }
        expect(TypeTarifEnum::tryFrom('invalid'))->toBeNull();

        // Test custom methods
        $values = TypeTarifEnum::values();
        expect($values)->toBeArray();
        expect($values)->toContain('normal', 'reduit', 'enfant', 'senior', 'etudiant', 'groupe');

        $options = TypeTarifEnum::options();
        expect($options)->toBeArray();

        // Test all labels
        expect(TypeTarifEnum::NORMAL->label())->toBe('Tarif normal');
        expect(TypeTarifEnum::REDUIT->label())->toBe('Tarif réduit');
        expect(TypeTarifEnum::ENFANT->label())->toBe('Tarif enfant');
        expect(TypeTarifEnum::SENIOR->label())->toBe('Tarif senior');
        expect(TypeTarifEnum::ETUDIANT->label())->toBe('Tarif étudiant');
        expect(TypeTarifEnum::GROUPE->label())->toBe('Tarif groupe');

        // Test reduction percentages
        expect(TypeTarifEnum::NORMAL->getReductionPercent())->toBe(0);
        expect(TypeTarifEnum::REDUIT->getReductionPercent())->toBe(20);
        expect(TypeTarifEnum::ENFANT->getReductionPercent())->toBe(30);
        expect(TypeTarifEnum::SENIOR->getReductionPercent())->toBe(25);
        expect(TypeTarifEnum::ETUDIANT->getReductionPercent())->toBe(25);
        expect(TypeTarifEnum::GROUPE->getReductionPercent())->toBe(15);

        // Test conditions
        expect(TypeTarifEnum::NORMAL->conditions())->toBe('Plein tarif');
        expect(TypeTarifEnum::REDUIT->conditions())->toBe('Sur présentation de justificatif');
        expect(TypeTarifEnum::ENFANT->conditions())->toBe('Moins de 14 ans');
        expect(TypeTarifEnum::SENIOR->conditions())->toBe('Plus de 65 ans');
        expect(TypeTarifEnum::ETUDIANT->conditions())->toBe('Sur présentation de la carte étudiant');
        expect(TypeTarifEnum::GROUPE->conditions())->toBe('À partir de 10 personnes');
    });
});
