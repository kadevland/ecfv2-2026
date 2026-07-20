<?php

declare(strict_types=1);

use App\Domain\Cinema\Entities\Salle;
use App\Domain\Cinema\Enums\StatutSalle;
use App\Domain\Cinema\Enums\QualiteSonore;
use App\Domain\Cinema\Events\SalleCreated;
use App\Domain\Cinema\Events\SalleUpdated;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Cinema\Enums\QualiteProjection;

describe('Salle Entity', function () {

    describe('Création et Construction', function () {

        it('peut créer une salle avec données minimales', function () {
            $cinemaId = CinemaId::generate();

            $salle = Salle::create(
                cinemaId: $cinemaId,
                nom: 'Salle 1',
                capaciteTotale: 150,
                nombreRangees: 10,
                placesParRangee: 15,
                placesStandard: 140,
                placesPmr: 10
            );

            expect($salle->id)->toBeInstanceOf(SalleId::class);
            expect($salle->cinemaId)->toBe($cinemaId);
            expect($salle->nom)->toBe('Salle 1');
            expect($salle->capaciteTotale)->toBe(150);
            expect($salle->nombreRangees)->toBe(10);
            expect($salle->placesParRangee)->toBe(15);
            expect($salle->placesStandard)->toBe(140);
            expect($salle->placesPmr)->toBe(10);
            expect($salle->statut)->toBe(StatutSalle::ACTIVE);
            expect($salle->accessibilitePmr)->toBeFalse();
            expect($salle->climatisation)->toBeTrue();
            expect($salle->qualiteProjection)->toBeArray()->toBeEmpty();
            expect($salle->qualiteSonore)->toBeArray()->toBeEmpty();
            expect($salle->planSalle)->toBeNull();
        });

        it('peut créer une salle avec toutes les options', function () {
            $cinemaId  = CinemaId::generate();
            $planSalle = ['type' => 'standard', 'rangees' => 10];

            $salle = Salle::create(
                cinemaId: $cinemaId,
                nom: 'Salle Premium IMAX',
                capaciteTotale: 200,
                nombreRangees: 15,
                placesParRangee: 14,
                placesStandard: 180,
                placesPmr: 20,
                qualiteProjection: [QualiteProjection::IMAX, QualiteProjection::DOLBY_VISION],
                qualiteSonore: [QualiteSonore::DOLBY_ATMOS, QualiteSonore::DTS_X],
                accessibilitePmr: true,
                climatisation: true,
                planSalle: $planSalle,
                statut: StatutSalle::ACTIVE
            );

            expect($salle->nom)->toBe('Salle Premium IMAX');
            expect($salle->capaciteTotale)->toBe(200);
            expect($salle->accessibilitePmr)->toBeTrue();
            expect($salle->qualiteProjection)->toHaveCount(2);
            expect($salle->qualiteSonore)->toHaveCount(2);
            expect($salle->planSalle)->toBe($planSalle);
            expect($salle->isPremium())->toBeTrue();
        });

        it('génère un ID unique automatiquement', function () {
            $cinemaId = CinemaId::generate();

            $salle1 = Salle::create($cinemaId, 'Salle 1', 100, 8, 12, 90, 10);
            $salle2 = Salle::create($cinemaId, 'Salle 2', 120, 9, 13, 110, 10);

            expect($salle1->id)->not->toBe($salle2->id);
            expect($salle1->id->getValue())->toBeString();
            expect($salle2->id->getValue())->toBeString();
        });

        it('émet un événement SalleCreated à la création', function () {
            $cinemaId = CinemaId::generate();

            $salle = Salle::create($cinemaId, 'Salle Test', 100, 8, 12, 90, 10);

            $events = $salle->getDomainEvents();
            expect($events)->toHaveCount(1);
            expect($events[0])->toBeInstanceOf(SalleCreated::class);
        });
    });

    describe('Gestion du Statut', function () {

        it('est disponible quand active', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Test',
                100, 8, 12, 90, 10,
                statut: StatutSalle::ACTIVE
            );

            expect($salle->isAvailable())->toBeTrue();
            expect($salle->estDisponible())->toBeTrue();
            expect($salle->isUnderMaintenance())->toBeFalse();
        });

        it('n\'est pas disponible en maintenance', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Test',
                100, 8, 12, 90, 10
            );

            $salle->demarrerMaintenance();

            expect($salle->isAvailable())->toBeFalse();
            expect($salle->isUnderMaintenance())->toBeTrue();
            expect($salle->statut)->toBe(StatutSalle::MAINTENANCE);
        });

        it('peut passer en rénovation', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Test',
                100, 8, 12, 90, 10
            );

            $salle->demarrerRenovation();

            expect($salle->statut)->toBe(StatutSalle::RENOVATION);
            expect($salle->isAvailable())->toBeFalse();
        });

        it('peut être mise hors service', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Test',
                100, 8, 12, 90, 10
            );

            $salle->mettreHorsService();

            expect($salle->statut)->toBe(StatutSalle::HORS_SERVICE);
            expect($salle->isAvailable())->toBeFalse();
        });

        it('peut être réactivée après maintenance', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Test',
                100, 8, 12, 90, 10
            );

            $salle->demarrerMaintenance();
            expect($salle->isAvailable())->toBeFalse();

            $salle->activer();
            expect($salle->isAvailable())->toBeTrue();
            expect($salle->statut)->toBe(StatutSalle::ACTIVE);
        });
    });

    describe('Gestion des Qualités', function () {

        it('peut ajouter des qualités de projection', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Test',
                100, 8, 12, 90, 10
            );

            $salle->ajouterQualiteProjection(QualiteProjection::IMAX);
            $salle->ajouterQualiteProjection(QualiteProjection::DOLBY_VISION);

            expect($salle->hasQualiteProjection(QualiteProjection::IMAX))->toBeTrue();
            expect($salle->hasQualiteProjection(QualiteProjection::DOLBY_VISION))->toBeTrue();
            expect($salle->hasQualiteProjection(QualiteProjection::STANDARD))->toBeFalse();
            expect($salle->qualiteProjection)->toHaveCount(2);
        });

        it('peut supprimer des qualités de projection', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Test',
                100, 8, 12, 90, 10,
                qualiteProjection: [QualiteProjection::IMAX, QualiteProjection::DOLBY_VISION]
            );

            expect($salle->qualiteProjection)->toHaveCount(2);

            $salle->supprimerQualiteProjection(QualiteProjection::IMAX);

            expect($salle->hasQualiteProjection(QualiteProjection::IMAX))->toBeFalse();
            expect($salle->hasQualiteProjection(QualiteProjection::DOLBY_VISION))->toBeTrue();
            expect($salle->qualiteProjection)->toHaveCount(1);
        });

        it('peut ajouter des qualités sonores', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Test',
                100, 8, 12, 90, 10
            );

            $salle->ajouterQualiteSonore(QualiteSonore::DOLBY_ATMOS);
            $salle->ajouterQualiteSonore(QualiteSonore::DTS_X);

            expect($salle->hasQualiteSonore(QualiteSonore::DOLBY_ATMOS))->toBeTrue();
            expect($salle->hasQualiteSonore(QualiteSonore::DTS_X))->toBeTrue();
            expect($salle->qualiteSonore)->toHaveCount(2);
        });

        it('peut supprimer des qualités sonores', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Test',
                100, 8, 12, 90, 10,
                qualiteSonore: [QualiteSonore::DOLBY_ATMOS, QualiteSonore::DTS_X]
            );

            $salle->supprimerQualiteSonore(QualiteSonore::DOLBY_ATMOS);

            expect($salle->hasQualiteSonore(QualiteSonore::DOLBY_ATMOS))->toBeFalse();
            expect($salle->hasQualiteSonore(QualiteSonore::DTS_X))->toBeTrue();
            expect($salle->qualiteSonore)->toHaveCount(1);
        });

        it('n\'ajoute pas de doublons de qualités', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Test',
                100, 8, 12, 90, 10
            );

            $salle->ajouterQualiteProjection(QualiteProjection::IMAX);
            $salle->ajouterQualiteProjection(QualiteProjection::IMAX); // Doublon

            expect($salle->qualiteProjection)->toHaveCount(1);
            expect($salle->hasQualiteProjection(QualiteProjection::IMAX))->toBeTrue();
        });
    });

    describe('Logique Métier Premium', function () {

        it('est premium avec IMAX', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle IMAX',
                200, 12, 16, 180, 20,
                qualiteProjection: [QualiteProjection::IMAX]
            );

            expect($salle->isPremium())->toBeTrue();
        });

        it('est premium avec Dolby Vision', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Dolby',
                150, 10, 15, 140, 10,
                qualiteProjection: [QualiteProjection::DOLBY_VISION]
            );

            expect($salle->isPremium())->toBeTrue();
        });

        it('est premium avec Dolby Atmos', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Atmos',
                150, 10, 15, 140, 10,
                qualiteSonore: [QualiteSonore::DOLBY_ATMOS]
            );

            expect($salle->isPremium())->toBeTrue();
        });

        it('n\'est pas premium en standard', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Standard',
                100, 8, 12, 90, 10,
                qualiteProjection: [QualiteProjection::STANDARD],
                qualiteSonore: [QualiteSonore::STEREO]
            );

            expect($salle->isPremium())->toBeFalse();
        });
    });

    describe('Modification des Propriétés', function () {

        it('peut changer le nom', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Ancien Nom',
                100, 8, 12, 90, 10
            );

            $salle->changerNom('Nouveau Nom');

            expect($salle->nom)->toBe('Nouveau Nom');
        });

        it('peut changer la capacité', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Test',
                100, 8, 12, 90, 10
            );

            $salle->changerCapacite(150);

            expect($salle->capaciteTotale)->toBe(150);
        });

        it('émet des événements lors des modifications', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Test',
                100, 8, 12, 90, 10
            );

            $salle->clearDomainEvents(); // Effacer l'événement de création

            $salle->changerNom('Nouveau Nom');
            $salle->changerCapacite(150);

            $events = $salle->getDomainEvents();
            expect($events)->toHaveCount(2);
            expect($events[0])->toBeInstanceOf(SalleUpdated::class);
            expect($events[1])->toBeInstanceOf(SalleUpdated::class);
        });
    });

    describe('Méthodes d\'Affichage', function () {

        it('formate correctement la capacité', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Test',
                1500, 20, 75, 1400, 100
            );

            expect($salle->getFormattedCapacity())->toBe('1 500 places');
        });

        it('retourne la chaîne des qualités de projection', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Test',
                100, 8, 12, 90, 10,
                qualiteProjection: [QualiteProjection::IMAX, QualiteProjection::DOLBY_VISION]
            );

            $qualites = $salle->getQualiteProjectionString();
            expect($qualites)->toContain('IMAX');
            expect($qualites)->toContain('Dolby Vision');
        });

        it('retourne la chaîne des qualités sonores', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Test',
                100, 8, 12, 90, 10,
                qualiteSonore: [QualiteSonore::DOLBY_ATMOS, QualiteSonore::DTS_X]
            );

            $qualites = $salle->getQualiteSonoreString();
            expect($qualites)->toContain('Dolby Atmos');
            expect($qualites)->toContain('DTS:X');
        });
    });

    describe('Méthodes Update CQRS', function () {

        it('peut mettre à jour via les méthodes CQRS', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Test',
                100, 8, 12, 90, 10
            );

            $salle->clearDomainEvents();

            $salle->updateNom('Nom Modifié');
            $salle->updateCapaciteTotale(200);
            $salle->updateAccessibilitePmr(true);

            expect($salle->nom)->toBe('Nom Modifié');
            expect($salle->capaciteTotale)->toBe(200);
            expect($salle->accessibilitePmr)->toBeTrue();

            $events = $salle->getDomainEvents();
            expect($events)->toHaveCount(3);
            expect($events[0])->toBeInstanceOf(SalleUpdated::class);
        });

        it('peut mettre à jour le plan de salle', function () {
            $salle = Salle::create(
                CinemaId::generate(),
                'Salle Test',
                100, 8, 12, 90, 10
            );

            $nouveauPlan = [
                'type'          => 'premium',
                'rangees'       => 12,
                'configuration' => 'escalier',
            ];

            $salle->updatePlanSalle($nouveauPlan);

            expect($salle->planSalle)->toBe($nouveauPlan);
        });
    });
});
