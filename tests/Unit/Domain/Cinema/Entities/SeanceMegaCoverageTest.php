<?php

declare(strict_types=1);

use App\Domain\Enums\StatutSeance;
use App\Domain\Cinema\Entities\Seance;
use App\Domain\Cinema\ValueObjects\FilmId;
use App\Domain\Shared\ValueObjects\Devise;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Domain\Shared\ValueObjects\TauxTva;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Domain\Cinema\ValueObjects\Tarification;

describe('Seance Entity - Real Coverage Tests', function () {
    it('constructor works correctly', function () {
        $tarifs = ['normal' => 1200];
        $seance = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-01-15 20:00'),
            new \DateTime('2025-01-15 22:00'),
            'VF',
            Tarification::create($tarifs),
            new TauxTva(2000), // 20% en basis points
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        expect($seance)->toBeInstanceOf(Seance::class);
        expect($seance->version)->toBe('VF');
        expect($seance->placementLibre)->toBeTrue();
        expect($seance->statut)->toBe(StatutSeance::PROGRAMMEE);
    });

    it('programmer static method works', function () {
        $tarifs = ['normal' => 1500];
        $seance = Seance::programmer(
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-02-01 14:00'),
            new \DateTime('2025-02-01 16:30'),
            'VOSTFR',
            Tarification::create($tarifs),
            new TauxTva(2000),
            new Devise('EUR'),
            false
        );

        expect($seance)->toBeInstanceOf(Seance::class);
        expect($seance->version)->toBe('VOSTFR');
        expect($seance->placementLibre)->toBeFalse();
        expect($seance->statut)->toBe(StatutSeance::PROGRAMMEE);
    });

    it('getTarification method works', function () {
        $tarifs       = ['normal' => 1200, 'reduit' => 900];
        $tarification = Tarification::create($tarifs);
        $seance       = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-01-15 20:00'),
            new \DateTime('2025-01-15 22:00'),
            'VF',
            $tarification,
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        expect($seance->getTarification())->toBe($tarification);
    });

    it('changerStatut method works', function () {
        $seance = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-01-15 20:00'),
            new \DateTime('2025-01-15 22:00'),
            'VF',
            Tarification::create(['normal' => 1200]),
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        $seance->changerStatut(StatutSeance::ANNULEE);
        expect($seance->statut)->toBe(StatutSeance::ANNULEE);
    });

    it('annuler method works', function () {
        $seance = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-01-15 20:00'),
            new \DateTime('2025-01-15 22:00'),
            'VF',
            Tarification::create(['normal' => 1200]),
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        $seance->annuler();
        expect($seance->statut)->toBe(StatutSeance::ANNULEE);
    });

    it('isPast method works', function () {
        $pastSeance = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2020-01-01 20:00'),
            new \DateTime('2020-01-01 22:00'),
            'VF',
            Tarification::create(['normal' => 1200]),
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        expect($pastSeance->isPast())->toBeTrue();
    });

    it('isUpcoming method works', function () {
        $futureSeance = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2030-01-01 20:00'),
            new \DateTime('2030-01-01 22:00'),
            'VF',
            Tarification::create(['normal' => 1200]),
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        expect($futureSeance->isUpcoming())->toBeTrue();
    });

    it('isPlaying method works', function () {
        $now           = new \DateTime;
        $playingSeance = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            $now->modify('-1 hour'),
            $now->modify('+1 hour'),
            'VF',
            Tarification::create(['normal' => 1200]),
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        expect($playingSeance->isPlaying())->toBeTrue();
    });

    it('prix normal method work', function () {
        $tarifs = ['normal' => 1200];
        $seance = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-01-15 20:00'),
            new \DateTime('2025-01-15 22:00'),
            'VF',
            Tarification::create($tarifs),
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        expect($seance->getPrixNormal())->not->toBeNull();
    });

    it('getFormattedTime works', function () {
        $seance = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-01-15 20:30'),
            new \DateTime('2025-01-15 22:00'),
            'VF',
            Tarification::create(['normal' => 1200]),
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        expect($seance->getFormattedTime())->toBe('20:30');
    });

    it('getSalleType works', function () {
        $seanceLibre = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-01-15 20:00'),
            new \DateTime('2025-01-15 22:00'),
            'VF',
            Tarification::create(['normal' => 1200]),
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        $seanceNumerotee = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-01-15 20:00'),
            new \DateTime('2025-01-15 22:00'),
            'VF',
            Tarification::create(['normal' => 1200]),
            new TauxTva(2000),
            new Devise('EUR'),
            false,
            StatutSeance::PROGRAMMEE
        );

        expect($seanceLibre->getSalleType())->toBe('libre');
        expect($seanceNumerotee->getSalleType())->toBe('numerotee');
    });

    it('getPlacesDisponibles works', function () {
        $seance = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-01-15 20:00'),
            new \DateTime('2025-01-15 22:00'),
            'VF',
            Tarification::create(['normal' => 1200]),
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        expect($seance->getPlacesDisponibles())->toBe(50);
    });

    it('getPlacesOccupees works', function () {
        $seance = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-01-15 20:00'),
            new \DateTime('2025-01-15 22:00'),
            'VF',
            Tarification::create(['normal' => 1200]),
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        expect($seance->getPlacesOccupees())->toBeArray();
        expect($seance->getPlacesOccupees())->toHaveCount(0);
    });

    it('getSalleConfiguration works', function () {
        $seance = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-01-15 20:00'),
            new \DateTime('2025-01-15 22:00'),
            'VF',
            Tarification::create(['normal' => 1200]),
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        $config = $seance->getSalleConfiguration();
        expect($config)->toBeArray();
        expect($config)->toHaveKey('rangees');
        expect($config)->toHaveKey('places_par_rangee');
        expect($config)->toHaveKey('total_places');
        expect($config['total_places'])->toBe(40);
    });

    it('reporter method works', function () {
        $seance = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-01-15 20:00'),
            new \DateTime('2025-01-15 22:00'),
            'VF',
            Tarification::create(['normal' => 1200]),
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        $nouvelleHeure = new \DateTime('2025-01-15 21:00');
        $nouvelleFin   = new \DateTime('2025-01-15 23:00');

        $seance->reporter($nouvelleHeure, $nouvelleFin);

        expect($seance->dateHeureDebut)->toBe($nouvelleHeure);
        expect($seance->dateHeureFin)->toBe($nouvelleFin);
    });

    it('updateVersion method works', function () {
        $seance = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-01-15 20:00'),
            new \DateTime('2025-01-15 22:00'),
            'VF',
            Tarification::create(['normal' => 1200]),
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        $seance->updateVersion('VOSTFR');
        expect($seance->version)->toBe('VOSTFR');
    });

    it('updatePlacementLibre method works', function () {
        $seance = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-01-15 20:00'),
            new \DateTime('2025-01-15 22:00'),
            'VF',
            Tarification::create(['normal' => 1200]),
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        $seance->updatePlacementLibre(false);
        expect($seance->placementLibre)->toBeFalse();
    });

    it('getDateHeureFin works', function () {
        $dateFin = new \DateTime('2025-01-15 22:00');
        $seance  = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-01-15 20:00'),
            $dateFin,
            'VF',
            Tarification::create(['normal' => 1200]),
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        expect($seance->getDateHeureFin())->toBe($dateFin);
    });

    it('updateDateHeureDebut method works', function () {
        $seance = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-01-15 20:00'),
            new \DateTime('2025-01-15 22:00'),
            'VF',
            Tarification::create(['normal' => 1200]),
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        $nouvelleHeureDebut = new \DateTime('2025-01-15 21:00');
        $seance->updateDateHeureDebut($nouvelleHeureDebut);

        expect($seance->dateHeureDebut)->toBe($nouvelleHeureDebut);
    });

    it('updateDateHeureDebutAvecFin method works', function () {
        $seance = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-01-15 20:00'),
            new \DateTime('2025-01-15 22:00'),
            'VF',
            Tarification::create(['normal' => 1200]),
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        $nouvelleHeureDebut = new \DateTime('2025-01-15 21:00');
        $nouvelleHeureFin   = new \DateTime('2025-01-15 23:00');

        $seance->updateDateHeureDebutAvecFin($nouvelleHeureDebut, $nouvelleHeureFin);

        expect($seance->dateHeureDebut)->toBe($nouvelleHeureDebut);
        expect($seance->dateHeureFin)->toBe($nouvelleHeureFin);
    });

    it('updateDureeAdditionnelle method works', function () {
        $seance = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-01-15 20:00'),
            new \DateTime('2025-01-15 22:00'),
            'VF',
            Tarification::create(['normal' => 1200]),
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        $seance->updateDureeAdditionnelle(15);
        expect($seance->dureeAdditionnelle)->toBe(15);
    });

    it('updateStatut method works', function () {
        $seance = new Seance(
            SeanceId::generate(),
            FilmId::generate(),
            SalleId::generate(),
            new \DateTime('2025-01-15 20:00'),
            new \DateTime('2025-01-15 22:00'),
            'VF',
            Tarification::create(['normal' => 1200]),
            new TauxTva(2000),
            new Devise('EUR'),
            true,
            StatutSeance::PROGRAMMEE
        );

        $seance->updateStatut(StatutSeance::EN_COURS);
        expect($seance->statut)->toBe(StatutSeance::EN_COURS);
    });
});
