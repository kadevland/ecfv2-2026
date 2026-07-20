<?php

declare(strict_types=1);

use Money\Money;
use Money\Currency;
use App\Domain\Shared\ValueObjects\Prix;
use App\Domain\Shared\ValueObjects\TauxTva;

describe('Prix ValueObject', function () {

    describe('Création à partir de HT', function () {

        it('peut créer un prix HT avec TVA', function () {
            $montantHT = new Money(1000, new Currency('EUR')); // 10.00€
            $tauxTva   = TauxTva::create(20.0);

            $prix = Prix::fromHT($montantHT, $tauxTva);

            expect($prix->montantHT)->toBe($montantHT);
            expect($prix->tauxTva)->toBe($tauxTva);
        });

        it('calcule le montant TTC correctement', function () {
            $montantHT = new Money(1000, new Currency('EUR')); // 10.00€
            $tauxTva   = TauxTva::create(20.0);

            $prix       = Prix::fromHT($montantHT, $tauxTva);
            $montantTTC = $prix->getMontantTTC();

            expect($montantTTC->getAmount())->toBe(1200); // 12.00€
        });
    });

    describe('Création à partir de TTC', function () {

        it('peut créer un prix TTC avec TVA', function () {
            $montantTTC = new Money(1200, new Currency('EUR')); // 12.00€
            $tauxTva    = TauxTva::create(20.0);

            $prix      = Prix::fromTTC($montantTTC, $tauxTva);
            $montantHT = $prix->montantHT;

            expect($montantHT->getAmount())->toBe(1000); // 10.00€ calculé
            expect($prix->tauxTva)->toBe($tauxTva);
        });
    });

    describe('Calculs avec différents taux de TVA', function () {

        it('calcule correctement avec TVA 5.5%', function () {
            $montantHT = new Money(1000, new Currency('EUR')); // 10.00€
            $tauxTva   = TauxTva::create(5.5);

            $prix       = Prix::fromHT($montantHT, $tauxTva);
            $montantTTC = $prix->getMontantTTC();

            expect($montantTTC->getAmount())->toBe(1055); // 10.55€
        });

        it('calcule correctement avec TVA 0%', function () {
            $montantHT = new Money(1000, new Currency('EUR')); // 10.00€
            $tauxTva   = TauxTva::create(0.0);

            $prix       = Prix::fromHT($montantHT, $tauxTva);
            $montantTTC = $prix->getMontantTTC();

            expect($montantTTC->getAmount())->toBe(1000); // Identique
        });
    });

    describe('Méthodes d\'affichage', function () {

        it('formate le prix HT', function () {
            $montantHT = new Money(1500, new Currency('EUR')); // 15.00€
            $tauxTva   = TauxTva::create(20.0);

            $prix = Prix::fromHT($montantHT, $tauxTva);

            expect($prix->formatHT())->toContain('15');
            expect($prix->formatHT())->toContain('€');
        });

        it('formate le prix TTC', function () {
            $montantHT = new Money(1000, new Currency('EUR')); // 10.00€
            $tauxTva   = TauxTva::create(20.0);

            $prix = Prix::fromHT($montantHT, $tauxTva);

            expect($prix->formatTTC())->toContain('12'); // 12.00€ TTC
            expect($prix->formatTTC())->toContain('€');
        });

        it('toString retourne le format TTC', function () {
            $montantHT = new Money(1000, new Currency('EUR'));
            $tauxTva   = TauxTva::create(20.0);

            $prix = Prix::fromHT($montantHT, $tauxTva);

            expect((string) $prix)->toBe($prix->formatTTC());
        });
    });

    describe('Comparaison de prix', function () {

        it('compare deux prix identiques', function () {
            $montantHT = new Money(1000, new Currency('EUR'));
            $tauxTva   = TauxTva::create(20.0);

            $prix1 = Prix::fromHT($montantHT, $tauxTva);
            $prix2 = Prix::fromHT($montantHT, $tauxTva);

            expect($prix1->equals($prix2))->toBeTrue();
        });

        it('détecte deux prix différents', function () {
            $montantHT1 = new Money(1000, new Currency('EUR'));
            $montantHT2 = new Money(1500, new Currency('EUR'));
            $tauxTva    = TauxTva::create(20.0);

            $prix1 = Prix::fromHT($montantHT1, $tauxTva);
            $prix2 = Prix::fromHT($montantHT2, $tauxTva);

            expect($prix1->equals($prix2))->toBeFalse();
        });
    });

    describe('Opérations arithmétiques', function () {

        it('peut ajouter deux prix', function () {
            $montantHT1 = new Money(1000, new Currency('EUR')); // 10.00€
            $montantHT2 = new Money(500, new Currency('EUR'));  // 5.00€
            $tauxTva    = TauxTva::create(20.0);

            $prix1 = Prix::fromHT($montantHT1, $tauxTva);
            $prix2 = Prix::fromHT($montantHT2, $tauxTva);

            $total = $prix1->add($prix2);

            expect($total->montantHT->getAmount())->toBe(1500); // 15.00€ HT
            expect($total->getMontantTTC()->getAmount())->toBe(1800); // 18.00€ TTC
        });

        it('peut multiplier un prix', function () {
            $montantHT = new Money(1000, new Currency('EUR')); // 10.00€
            $tauxTva   = TauxTva::create(20.0);

            $prix         = Prix::fromHT($montantHT, $tauxTva);
            $prixMultiple = $prix->multiply(3);

            expect($prixMultiple->montantHT->getAmount())->toBe(3000); // 30.00€ HT
            expect($prixMultiple->getMontantTTC()->getAmount())->toBe(3600); // 36.00€ TTC
        });
    });
});
