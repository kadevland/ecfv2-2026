<?php

declare(strict_types=1);

use App\Domain\Enums\StatutSeance;
use App\Domain\Cinema\Entities\Seance;
use App\Domain\Cinema\Enums\QualiteSonore;
use App\Domain\Cinema\ValueObjects\FilmId;
use App\Domain\Shared\ValueObjects\Devise;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Domain\Shared\ValueObjects\TauxTva;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Domain\Cinema\Enums\QualiteProjection;
use App\Domain\Cinema\ValueObjects\Tarification;

describe('Seance Entity', function () {

    function createValidSeance(): Seance
    {
        $debut = new DateTime('2025-12-15 20:00:00');
        $fin   = new DateTime('2025-12-15 22:30:00');

        return new Seance(
            id: SeanceId::generate(),
            filmId: FilmId::generate(),
            salleId: SalleId::generate(),
            dateHeureDebut: $debut,
            dateHeureFin: $fin,
            version: 'VF',
            tarification: Tarification::create(['NORMAL' => 1200, 'REDUIT' => 900]),
            tauxTva: TauxTva::create(20.0),
            devise: Devise::EUR()
        );
    }

    describe('Création et Construction', function () {

        it('peut créer une séance avec données valides', function () {
            $seanceId     = SeanceId::generate();
            $filmId       = FilmId::generate();
            $salleId      = SalleId::generate();
            $debut        = new DateTime('2025-12-15 18:00:00');
            $fin          = new DateTime('2025-12-15 20:30:00');
            $tarification = Tarification::create(['NORMAL' => 1200]);
            $tauxTva      = TauxTva::create(20.0);

            $seance = new Seance(
                id: $seanceId,
                filmId: $filmId,
                salleId: $salleId,
                dateHeureDebut: $debut,
                dateHeureFin: $fin,
                version: 'VO',
                tarification: $tarification,
                tauxTva: $tauxTva,
                devise: Devise::EUR()
            );

            expect($seance->id)->toBe($seanceId);
            expect($seance->filmId)->toBe($filmId);
            expect($seance->salleId)->toBe($salleId);
            expect($seance->dateHeureDebut)->toBe($debut);
            expect($seance->dateHeureFin)->toBe($fin);
            expect($seance->version)->toBe('VO');
            expect($seance->tarification)->toBe($tarification);
            expect($seance->tauxTva)->toBe($tauxTva);
            expect($seance->statut)->toBe(StatutSeance::PROGRAMMEE);
            expect($seance->placementLibre)->toBeFalse();
            expect($seance->dureeAdditionnelle)->toBeNull();
        });

        it('peut créer avec options avancées', function () {
            $seance = new Seance(
                id: SeanceId::generate(),
                filmId: FilmId::generate(),
                salleId: SalleId::generate(),
                dateHeureDebut: new DateTime('2025-12-15 20:00:00'),
                dateHeureFin: new DateTime('2025-12-15 22:30:00'),
                version: 'VFSTF',
                tarification: Tarification::create(['PREMIUM' => 1800]),
                tauxTva: TauxTva::create(5.5),
                devise: Devise::EUR(),
                placementLibre: true,
                statut: StatutSeance::EN_COURS,
                dureeAdditionnelle: 15,
                qualiteProjection: QualiteProjection::IMAX,
                qualiteSonore: QualiteSonore::DOLBY_ATMOS
            );

            expect($seance->placementLibre)->toBeTrue();
            expect($seance->statut)->toBe(StatutSeance::EN_COURS);
            expect($seance->dureeAdditionnelle)->toBe(15);
            expect($seance->qualiteProjection)->toBe(QualiteProjection::IMAX);
            expect($seance->qualiteSonore)->toBe(QualiteSonore::DOLBY_ATMOS);
        });

        it('génère des IDs uniques', function () {
            $seance1 = createValidSeance();
            $seance2 = createValidSeance();

            expect($seance1->id)->not->toBe($seance2->id);
            expect($seance1->id->value)->toBeString();
            expect($seance2->id->value)->toBeString();
        });
    });

    describe('Propriétés de Base', function () {

        it('stocke correctement les dates', function () {
            $debut = new DateTime('2025-12-20 14:30:00');
            $fin   = new DateTime('2025-12-20 17:00:00');

            $seance = new Seance(
                id: SeanceId::generate(),
                filmId: FilmId::generate(),
                salleId: SalleId::generate(),
                dateHeureDebut: $debut,
                dateHeureFin: $fin,
                version: 'VF',
                tarification: Tarification::create(['NORMAL' => 1000]),
                tauxTva: TauxTva::create(20.0),
                devise: Devise::EUR()
            );

            expect($seance->dateHeureDebut->format('Y-m-d H:i:s'))->toBe('2025-12-20 14:30:00');
            expect($seance->dateHeureFin->format('Y-m-d H:i:s'))->toBe('2025-12-20 17:00:00');
        });

        it('gère les différentes versions linguistiques', function () {
            $versions = ['VF', 'VO', 'VOST', 'VFSTF'];

            foreach ($versions as $version) {
                $seance = new Seance(
                    id: SeanceId::generate(),
                    filmId: FilmId::generate(),
                    salleId: SalleId::generate(),
                    dateHeureDebut: new DateTime('2025-12-15 20:00:00'),
                    dateHeureFin: new DateTime('2025-12-15 22:00:00'),
                    version: $version,
                    tarification: Tarification::create(['NORMAL' => 1200]),
                    tauxTva: TauxTva::create(20.0),
                    devise: Devise::EUR()
                );

                expect($seance->version)->toBe($version);
            }
        });

        it('gère les différents statuts', function () {
            $statuts = [
                StatutSeance::PROGRAMMEE,
                StatutSeance::EN_COURS,
                StatutSeance::TERMINEE,
                StatutSeance::ANNULEE,
            ];

            foreach ($statuts as $statut) {
                $seance = new Seance(
                    id: SeanceId::generate(),
                    filmId: FilmId::generate(),
                    salleId: SalleId::generate(),
                    dateHeureDebut: new DateTime('2025-12-15 20:00:00'),
                    dateHeureFin: new DateTime('2025-12-15 22:00:00'),
                    version: 'VF',
                    tarification: Tarification::create(['NORMAL' => 1200]),
                    tauxTva: TauxTva::create(20.0),
                    devise: Devise::EUR(),
                    statut: $statut
                );

                expect($seance->statut)->toBe($statut);
            }
        });
    });

    describe('Qualités Audio/Vidéo', function () {

        it('peut avoir une qualité de projection IMAX', function () {
            $seance = new Seance(
                id: SeanceId::generate(),
                filmId: FilmId::generate(),
                salleId: SalleId::generate(),
                dateHeureDebut: new DateTime('2025-12-15 20:00:00'),
                dateHeureFin: new DateTime('2025-12-15 22:30:00'),
                version: 'VF',
                tarification: Tarification::create(['PREMIUM' => 1800]),
                tauxTva: TauxTva::create(20.0),
                devise: Devise::EUR(),
                qualiteProjection: QualiteProjection::IMAX
            );

            expect($seance->qualiteProjection)->toBe(QualiteProjection::IMAX);
        });

        it('peut avoir une qualité sonore Dolby Atmos', function () {
            $seance = new Seance(
                id: SeanceId::generate(),
                filmId: FilmId::generate(),
                salleId: SalleId::generate(),
                dateHeureDebut: new DateTime('2025-12-15 20:00:00'),
                dateHeureFin: new DateTime('2025-12-15 22:30:00'),
                version: 'VF',
                tarification: Tarification::create(['PREMIUM' => 1800]),
                tauxTva: TauxTva::create(20.0),
                devise: Devise::EUR(),
                qualiteSonore: QualiteSonore::DOLBY_ATMOS
            );

            expect($seance->qualiteSonore)->toBe(QualiteSonore::DOLBY_ATMOS);
        });

        it('peut avoir les deux qualités premium', function () {
            $seance = new Seance(
                id: SeanceId::generate(),
                filmId: FilmId::generate(),
                salleId: SalleId::generate(),
                dateHeureDebut: new DateTime('2025-12-15 20:00:00'),
                dateHeureFin: new DateTime('2025-12-15 22:30:00'),
                version: 'VO',
                tarification: Tarification::create(['PREMIUM' => 2000]),
                tauxTva: TauxTva::create(20.0),
                devise: Devise::EUR(),
                qualiteProjection: QualiteProjection::DOLBY_VISION,
                qualiteSonore: QualiteSonore::DTS_X
            );

            expect($seance->qualiteProjection)->toBe(QualiteProjection::DOLBY_VISION);
            expect($seance->qualiteSonore)->toBe(QualiteSonore::DTS_X);
        });
    });

    describe('Tarification', function () {

        it('stocke correctement la tarification', function () {
            $tarifs       = ['NORMAL' => 1200, 'REDUIT' => 900, 'ENFANT' => 700];
            $tarification = Tarification::create($tarifs);

            $seance = new Seance(
                id: SeanceId::generate(),
                filmId: FilmId::generate(),
                salleId: SalleId::generate(),
                dateHeureDebut: new DateTime('2025-12-15 20:00:00'),
                dateHeureFin: new DateTime('2025-12-15 22:00:00'),
                version: 'VF',
                tarification: $tarification,
                tauxTva: TauxTva::create(20.0),
                devise: Devise::EUR()
            );

            expect($seance->tarification)->toBe($tarification);
        });

        it('gère différents taux de TVA', function () {
            $tauxTva55 = TauxTva::create(5.5);
            $tauxTva20 = TauxTva::create(20.0);

            $seance1 = new Seance(
                id: SeanceId::generate(),
                filmId: FilmId::generate(),
                salleId: SalleId::generate(),
                dateHeureDebut: new DateTime('2025-12-15 20:00:00'),
                dateHeureFin: new DateTime('2025-12-15 22:00:00'),
                version: 'VF',
                tarification: Tarification::create(['NORMAL' => 1000]),
                tauxTva: $tauxTva55,
                devise: Devise::EUR()
            );

            $seance2 = new Seance(
                id: SeanceId::generate(),
                filmId: FilmId::generate(),
                salleId: SalleId::generate(),
                dateHeureDebut: new DateTime('2025-12-15 20:00:00'),
                dateHeureFin: new DateTime('2025-12-15 22:00:00'),
                version: 'VF',
                tarification: Tarification::create(['NORMAL' => 1000]),
                tauxTva: $tauxTva20,
                devise: Devise::EUR()
            );

            expect($seance1->tauxTva)->toBe($tauxTva55);
            expect($seance2->tauxTva)->toBe($tauxTva20);
        });

        it('utilise EUR par défaut comme devise', function () {
            $seance = createValidSeance();

            expect($seance->devise)->toBe(Devise::EUR());
        });
    });

    describe('Options de Placement', function () {

        it('a un placement numéroté par défaut', function () {
            $seance = createValidSeance();

            expect($seance->placementLibre)->toBeFalse();
        });

        it('peut avoir un placement libre', function () {
            $seance = new Seance(
                id: SeanceId::generate(),
                filmId: FilmId::generate(),
                salleId: SalleId::generate(),
                dateHeureDebut: new DateTime('2025-12-15 20:00:00'),
                dateHeureFin: new DateTime('2025-12-15 22:00:00'),
                version: 'VF',
                tarification: Tarification::create(['NORMAL' => 1200]),
                tauxTva: TauxTva::create(20.0),
                devise: Devise::EUR(),
                placementLibre: true
            );

            expect($seance->placementLibre)->toBeTrue();
        });
    });

    describe('Durée Additionnelle', function () {

        it('peut avoir une durée additionnelle nulle', function () {
            $seance = createValidSeance();

            expect($seance->dureeAdditionnelle)->toBeNull();
        });

        it('peut avoir une durée additionnelle définie', function () {
            $seance = new Seance(
                id: SeanceId::generate(),
                filmId: FilmId::generate(),
                salleId: SalleId::generate(),
                dateHeureDebut: new DateTime('2025-12-15 20:00:00'),
                dateHeureFin: new DateTime('2025-12-15 23:00:00'),
                version: 'VF',
                tarification: Tarification::create(['NORMAL' => 1200]),
                tauxTva: TauxTva::create(20.0),
                devise: Devise::EUR(),
                dureeAdditionnelle: 30
            );

            expect($seance->dureeAdditionnelle)->toBe(30);
        });
    });
});
