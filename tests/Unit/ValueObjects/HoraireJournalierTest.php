<?php

declare(strict_types=1);

use App\Domain\Shared\ValueObjects\HoraireJournalier;

describe('HoraireJournalier Value Object', function () {

    describe('Factory methods', function () {

        it('can create a closed day', function () {
            $horaire = HoraireJournalier::ferme();

            expect($horaire->estFerme())->toBeTrue();
            expect($horaire->estOuvert())->toBeFalse();
            expect($horaire->debutMatin)->toBeNull();
            expect($horaire->finMatin)->toBeNull();
            expect($horaire->debutApres)->toBeNull();
            expect($horaire->finApres)->toBeNull();
        });

        it('can create a morning only schedule', function () {
            $horaire = HoraireJournalier::create(
                debutMatin: '09:00',
                finMatin: '12:00',
                dureeMaxSeanceMatin: 120
            );

            expect($horaire->estOuvert())->toBeTrue();
            expect($horaire->estFerme())->toBeFalse();
            expect($horaire->journeeComplete())->toBeTrue();
            expect($horaire->debutMatin)->toBe('09:00');
            expect($horaire->finMatin)->toBe('12:00');
            expect($horaire->dureeMaxSeanceMatin)->toBe(120);
        });

        it('can create a full day schedule', function () {
            $horaire = HoraireJournalier::create(
                debutMatin: '09:00',
                finMatin: '12:00',
                dureeMaxSeanceMatin: 120,
                debutApres: '14:00',
                finApres: '22:00',
                dureeMaxSeanceApres: 180
            );

            expect($horaire->estOuvert())->toBeTrue();
            expect($horaire->journeeComplete())->toBeFalse();
            expect($horaire->debutMatin)->toBe('09:00');
            expect($horaire->finMatin)->toBe('12:00');
            expect($horaire->debutApres)->toBe('14:00');
            expect($horaire->finApres)->toBe('22:00');
        });

    });

    describe('fromArray factory', function () {

        it('creates closed day when ouvert is false', function () {
            $data    = ['ouvert' => false];
            $horaire = HoraireJournalier::fromArray($data);

            expect($horaire->estFerme())->toBeTrue();
        });

        it('creates closed day when ouvert is missing', function () {
            $data    = [];
            $horaire = HoraireJournalier::fromArray($data);

            expect($horaire->estFerme())->toBeTrue();
        });

        it('creates valid morning schedule from array', function () {
            $data = [
                'ouvert'                 => true,
                'debut_matin'            => '09:00',
                'fin_matin'              => '12:00',
                'duree_max_seance_matin' => 120,
            ];

            $horaire = HoraireJournalier::fromArray($data);

            expect($horaire->estOuvert())->toBeTrue();
            expect($horaire->debutMatin)->toBe('09:00');
            expect($horaire->finMatin)->toBe('12:00');
            expect($horaire->dureeMaxSeanceMatin)->toBe(120);
        });

        it('creates valid full day schedule from array', function () {
            $data = [
                'ouvert'                 => true,
                'debut_matin'            => '09:00',
                'fin_matin'              => '12:00',
                'duree_max_seance_matin' => 120,
                'debut_apres'            => '14:00',
                'fin_apres'              => '22:00',
                'duree_max_seance_apres' => 180,
            ];

            $horaire = HoraireJournalier::fromArray($data);

            expect($horaire->estOuvert())->toBeTrue();
            expect($horaire->journeeComplete())->toBeFalse();
            expect($horaire->debutApres)->toBe('14:00');
            expect($horaire->finApres)->toBe('22:00');
        });

    });

    describe('Validation', function () {

        it('rejects invalid time format', function () {
            expect(fn () => HoraireJournalier::create(
                debutMatin: '25:00',
                finMatin: '12:00',
                dureeMaxSeanceMatin: 120
            ))->toThrow(InvalidArgumentException::class, 'Format d\'heure invalide');
        });

        it('rejects when morning start is after morning end', function () {
            expect(fn () => HoraireJournalier::create(
                debutMatin: '12:00',
                finMatin: '09:00',
                dureeMaxSeanceMatin: 120
            ))->toThrow(InvalidArgumentException::class, 'début matin doit être avant');
        });

        it('rejects when afternoon start is after afternoon end', function () {
            expect(fn () => HoraireJournalier::create(
                debutMatin: '09:00',
                finMatin: '12:00',
                dureeMaxSeanceMatin: 120,
                debutApres: '22:00',
                finApres: '14:00',
                dureeMaxSeanceApres: 180
            ))->toThrow(InvalidArgumentException::class, 'début après-midi doit être avant');
        });

        it('rejects when morning end is after afternoon start', function () {
            expect(fn () => HoraireJournalier::create(
                debutMatin: '09:00',
                finMatin: '15:00',
                dureeMaxSeanceMatin: 120,
                debutApres: '14:00',
                finApres: '22:00',
                dureeMaxSeanceApres: 180
            ))->toThrow(InvalidArgumentException::class, 'fin du matin ne peut pas être après le début');
        });

        it('rejects negative duration', function () {
            expect(fn () => HoraireJournalier::create(
                debutMatin: '09:00',
                finMatin: '12:00',
                dureeMaxSeanceMatin: -60
            ))->toThrow(InvalidArgumentException::class, 'durée max des séances matin doit être positive');
        });

        it('rejects incomplete morning schedule', function () {
            $data = [
                'ouvert'      => true,
                'debut_matin' => '09:00',
                'fin_matin'   => '12:00',
                // Missing duree_max_seance_matin
            ];

            expect(fn () => HoraireJournalier::fromArray($data))
                ->toThrow(InvalidArgumentException::class, 'tous les champs matin sont obligatoires');
        });

        it('rejects incomplete afternoon schedule', function () {
            $data = [
                'ouvert'                 => true,
                'debut_matin'            => '09:00',
                'fin_matin'              => '12:00',
                'duree_max_seance_matin' => 120,
                'debut_apres'            => '14:00',
                // Missing fin_apres and duree_max_seance_apres
            ];

            expect(fn () => HoraireJournalier::fromArray($data))
                ->toThrow(InvalidArgumentException::class, 'tous les champs après-midi sont obligatoires');
        });

    });

    describe('Business methods', function () {

        it('can get morning hours', function () {
            $horaire = HoraireJournalier::create(
                debutMatin: '09:00',
                finMatin: '12:00',
                dureeMaxSeanceMatin: 120
            );

            expect($horaire->getHoraireMatin())->toBe(['09:00', '12:00']);
        });

        it('returns null for afternoon hours when afternoon is not set', function () {
            $horaire = HoraireJournalier::create(
                debutMatin: '09:00',
                finMatin: '12:00',
                dureeMaxSeanceMatin: 120
            );

            expect($horaire->getHoraireApres())->toBeNull();
        });

        it('returns afternoon hours when set', function () {
            $horaire = HoraireJournalier::create(
                debutMatin: '09:00',
                finMatin: '12:00',
                dureeMaxSeanceMatin: 120,
                debutApres: '14:00',
                finApres: '22:00',
                dureeMaxSeanceApres: 180
            );

            expect($horaire->getHoraireApres())->toBe(['14:00', '22:00']);
        });

        it('can check if session can be scheduled in morning', function () {
            $horaire = HoraireJournalier::create(
                debutMatin: '09:00',
                finMatin: '12:00',
                dureeMaxSeanceMatin: 120
            );

            expect($horaire->peutProgrammerSeance('09:00', 120))->toBeTrue();
            expect($horaire->peutProgrammerSeance('09:00', 240))->toBeFalse(); // Too long
            expect($horaire->peutProgrammerSeance('08:00', 120))->toBeFalse(); // Too early
            expect($horaire->peutProgrammerSeance('13:00', 120))->toBeFalse(); // Too late
        });

        it('can check if session is in public hours', function () {
            $horaire = HoraireJournalier::create(
                debutMatin: '09:00',
                finMatin: '12:00',
                dureeMaxSeanceMatin: 120,
                debutApres: '14:00',
                finApres: '22:00',
                dureeMaxSeanceApres: 180
            );

            expect($horaire->seanceDansHorairesPublics('10:00'))->toBeTrue(); // Morning
            expect($horaire->seanceDansHorairesPublics('15:00'))->toBeTrue(); // Afternoon
            expect($horaire->seanceDansHorairesPublics('13:00'))->toBeFalse(); // Lunch break
            expect($horaire->seanceDansHorairesPublics('08:00'))->toBeFalse(); // Before opening
            expect($horaire->seanceDansHorairesPublics('23:00'))->toBeFalse(); // After closing
        });

    });

    describe('Serialization', function () {

        it('can convert to array', function () {
            $horaire = HoraireJournalier::create(
                debutMatin: '09:00',
                finMatin: '12:00',
                dureeMaxSeanceMatin: 120,
                debutApres: '14:00',
                finApres: '22:00',
                dureeMaxSeanceApres: 180
            );

            $array = $horaire->toArray();

            expect($array)->toBe([
                'debut_matin'            => '09:00',
                'fin_matin'              => '12:00',
                'duree_max_seance_matin' => 120,
                'debut_apres'            => '14:00',
                'fin_apres'              => '22:00',
                'duree_max_seance_apres' => 180,
            ]);
        });

        it('can get summary', function () {
            $horaire = HoraireJournalier::create(
                debutMatin: '09:00',
                finMatin: '12:00',
                dureeMaxSeanceMatin: 120,
                debutApres: '14:00',
                finApres: '22:00',
                dureeMaxSeanceApres: 180
            );

            expect($horaire->getSummary())->toBe('09:00-12:00, 14:00-22:00');
        });

        it('shows closed for closed day', function () {
            $horaire = HoraireJournalier::ferme();

            expect($horaire->getSummary())->toBe('Fermé');
        });

        it('shows morning only for complete day', function () {
            $horaire = HoraireJournalier::create(
                debutMatin: '09:00',
                finMatin: '17:00',
                dureeMaxSeanceMatin: 120
            );

            expect($horaire->getSummary())->toBe('09:00 - 17:00');
        });

    });

    describe('Equality', function () {

        it('can check equality with another horaire', function () {
            $horaire1 = HoraireJournalier::create(
                debutMatin: '09:00',
                finMatin: '12:00',
                dureeMaxSeanceMatin: 120
            );

            $horaire2 = HoraireJournalier::create(
                debutMatin: '09:00',
                finMatin: '12:00',
                dureeMaxSeanceMatin: 120
            );

            $horaire3 = HoraireJournalier::create(
                debutMatin: '10:00',
                finMatin: '12:00',
                dureeMaxSeanceMatin: 120
            );

            expect($horaire1->equals($horaire2))->toBeTrue();
            expect($horaire1->equals($horaire3))->toBeFalse();
        });

    });

});
