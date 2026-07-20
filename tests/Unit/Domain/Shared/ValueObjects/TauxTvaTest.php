<?php

declare(strict_types=1);

use InvalidArgumentException;
use App\Domain\Shared\ValueObjects\TauxTva;

describe('TauxTva ValueObject', function () {

    describe('Création et Validation', function () {

        it('accepte un taux de TVA standard 20%', function () {
            $taux = TauxTva::create(20.0);

            expect($taux->getPercentage())->toBe(20.0);
            expect($taux->getDecimal())->toBe(0.2);
        });

        it('accepte un taux de TVA réduit 5.5%', function () {
            $taux = TauxTva::create(5.5);

            expect($taux->getPercentage())->toBe(5.5);
            expect($taux->getDecimal())->toBe(0.055);
        });

        it('accepte un taux de TVA à 0%', function () {
            $taux = TauxTva::create(0.0);

            expect($taux->getPercentage())->toBe(0.0);
            expect($taux->getDecimal())->toBe(0.0);
        });

        it('rejette un taux négatif', function () {
            expect(fn () => TauxTva::create(-5.0))
                ->toThrow(InvalidArgumentException::class);
        });

        it('rejette un taux supérieur à 100%', function () {
            expect(fn () => TauxTva::create(105.0))
                ->toThrow(InvalidArgumentException::class);
        });
    });

    describe('Taux Prédéfinis', function () {

        it('fournit le taux standard français', function () {
            $taux = TauxTva::standard();

            expect($taux->getPercentage())->toBe(20.0);
        });

        it('fournit le taux réduit français', function () {
            $taux = TauxTva::reduit();

            expect($taux->getPercentage())->toBe(5.5);
        });

        it('fournit le taux super-réduit français', function () {
            $taux = TauxTva::superReduit();

            expect($taux->getPercentage())->toBe(2.1);
        });

        it('fournit le taux intermédiaire français', function () {
            $taux = TauxTva::intermediaire();

            expect($taux->getPercentage())->toBe(10.0);
        });
    });

    describe('Calculs', function () {

        it('calcule la TVA sur un montant HT', function () {
            $taux      = TauxTva::create(20.0);
            $montantHT = 1000; // 10.00€

            $montantTva = $taux->calculateTvaAmount($montantHT);

            expect($montantTva)->toBe(200); // 2.00€ de TVA
        });

        it('calcule le montant TTC à partir du HT', function () {
            $taux      = TauxTva::create(20.0);
            $montantHT = 1000; // 10.00€

            $montantTTC = $taux->addTvaToAmount($montantHT);

            expect($montantTTC)->toBe(1200); // 12.00€ TTC
        });

        it('calcule le montant HT à partir du TTC', function () {
            $taux       = TauxTva::create(20.0);
            $montantTTC = 1200; // 12.00€

            $montantHT = $taux->removeTvaFromAmount($montantTTC);

            expect($montantHT)->toBe(1000); // 10.00€ HT
        });

        it('calcule correctement avec des décimales', function () {
            $taux      = TauxTva::create(5.5);
            $montantHT = 1000; // 10.00€

            $montantTTC = $taux->addTvaToAmount($montantHT);

            expect($montantTTC)->toBe(1055); // 10.55€ TTC
        });
    });

    describe('Comparaison', function () {

        it('compare deux taux identiques', function () {
            $taux1 = TauxTva::create(20.0);
            $taux2 = TauxTva::create(20.0);

            expect($taux1->equals($taux2))->toBeTrue();
        });

        it('compare deux taux différents', function () {
            $taux1 = TauxTva::create(20.0);
            $taux2 = TauxTva::create(5.5);

            expect($taux1->equals($taux2))->toBeFalse();
        });

        it('détermine si un taux est standard', function () {
            $tauxStandard = TauxTva::create(20.0);
            $tauxReduit   = TauxTva::create(5.5);

            expect($tauxStandard->isStandard())->toBeTrue();
            expect($tauxReduit->isStandard())->toBeFalse();
        });
    });

    describe('Formatage', function () {

        it('formate le taux en pourcentage', function () {
            $taux = TauxTva::create(20.0);

            expect($taux->format())->toBe('20,00%');
        });

        it('formate le taux réduit en pourcentage', function () {
            $taux = TauxTva::create(5.5);

            expect($taux->format())->toBe('5,50%');
        });

        it('convertit en string', function () {
            $taux = TauxTva::create(10.0);

            expect((string) $taux)->toBe('10,00%');
        });
    });
});
