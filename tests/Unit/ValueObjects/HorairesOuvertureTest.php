<?php

declare(strict_types=1);

use App\Domain\Shared\Enums\JourSemaine;
use App\Domain\Shared\ValueObjects\HorairesOuverture;

describe('HorairesOuverture Value Object', function () {

    describe('Factory methods', function () {

        it('can create standard opening hours', function () {
            $horaires = HorairesOuverture::standard();

            expect($horaires->lundi->estOuvert())->toBeTrue();
            expect($horaires->mardi->estOuvert())->toBeTrue();
            expect($horaires->dimanche->estOuvert())->toBeTrue();

            expect($horaires->lundi->debutMatin)->toBe('09:00');
            expect($horaires->lundi->finMatin)->toBe('12:30');
            expect($horaires->lundi->debutApres)->toBe('15:00');
            expect($horaires->lundi->finApres)->toBe('22:30');
        });

        it('can create from array with all days', function () {
            $data = [
                'lundi' => [
                    'ouvert'                 => true,
                    'debut_matin'            => '09:00',
                    'fin_matin'              => '12:00',
                    'duree_max_seance_matin' => 120,
                    'debut_apres'            => '14:00',
                    'fin_apres'              => '22:00',
                    'duree_max_seance_apres' => 180,
                ],
                'mardi' => [
                    'ouvert' => false,
                ],
                // Other days will be closed by default
            ];

            $horaires = HorairesOuverture::fromArray($data);

            expect($horaires->lundi->estOuvert())->toBeTrue();
            expect($horaires->mardi->estFerme())->toBeTrue();
            expect($horaires->mercredi->estFerme())->toBeTrue();
        });

    });

    describe('Business methods', function () {

        it('can get horaire for specific day', function () {
            $data = [
                'lundi' => [
                    'ouvert'                 => true,
                    'debut_matin'            => '09:00',
                    'fin_matin'              => '12:00',
                    'duree_max_seance_matin' => 120,
                ],
            ];

            $horaires    = HorairesOuverture::fromArray($data);
            $horaireJour = $horaires->getHoraireJour(JourSemaine::LUNDI);

            expect($horaireJour->estOuvert())->toBeTrue();
            expect($horaireJour->debutMatin)->toBe('09:00');
        });

        it('can check if open on specific day', function () {
            $data = [
                'lundi' => [
                    'ouvert'                 => true,
                    'debut_matin'            => '09:00',
                    'fin_matin'              => '17:00',
                    'duree_max_seance_matin' => 120,
                ],
                'mardi' => [
                    'ouvert' => false,
                ],
            ];

            $horaires = HorairesOuverture::fromArray($data);

            expect($horaires->isOpenOn(JourSemaine::LUNDI))->toBeTrue();
            expect($horaires->isOpenOn(JourSemaine::MARDI))->toBeFalse();
            expect($horaires->isOpenOn(JourSemaine::MERCREDI))->toBeFalse();
        });

        it('can get all open days', function () {
            $data = [
                'lundi' => [
                    'ouvert'                 => true,
                    'debut_matin'            => '09:00',
                    'fin_matin'              => '17:00',
                    'duree_max_seance_matin' => 120,
                ],
                'mercredi' => [
                    'ouvert'                 => true,
                    'debut_matin'            => '09:00',
                    'fin_matin'              => '17:00',
                    'duree_max_seance_matin' => 120,
                ],
                'vendredi' => [
                    'ouvert'                 => true,
                    'debut_matin'            => '09:00',
                    'fin_matin'              => '17:00',
                    'duree_max_seance_matin' => 120,
                ],
            ];

            $horaires     = HorairesOuverture::fromArray($data);
            $joursOuverts = $horaires->getJoursOuverts();

            expect($joursOuverts)->toHaveCount(3);
            expect($joursOuverts)->toContain(JourSemaine::LUNDI);
            expect($joursOuverts)->toContain(JourSemaine::MERCREDI);
            expect($joursOuverts)->toContain(JourSemaine::VENDREDI);
            expect($joursOuverts)->not->toContain(JourSemaine::MARDI);
        });

    });

    describe('Serialization', function () {

        it('can convert to array', function () {
            $data = [
                'lundi' => [
                    'ouvert'                 => true,
                    'debut_matin'            => '09:00',
                    'fin_matin'              => '12:00',
                    'duree_max_seance_matin' => 120,
                ],
            ];

            $horaires = HorairesOuverture::fromArray($data);
            $array    = $horaires->toArray();

            expect($array)->toBeArray();
            expect($array)->toHaveKey('lundi');
            expect($array)->toHaveKey('mardi');
            expect($array['lundi'])->toHaveKey('debut_matin');
            expect($array['lundi']['debut_matin'])->toBe('09:00');
        });

        it('can convert to JSON', function () {
            $horaires = HorairesOuverture::standard();
            $json     = $horaires->toJson();

            expect($json)->toBeString();

            $decoded = json_decode($json, true);
            expect($decoded)->toBeArray();
            expect($decoded)->toHaveKey('lundi');
        });

    });

    describe('Validation', function () {

        it('requires at least one day open for strict validation', function () {
            $data = [
                'lundi'    => ['ouvert' => false],
                'mardi'    => ['ouvert' => false],
                'mercredi' => ['ouvert' => false],
                'jeudi'    => ['ouvert' => false],
                'vendredi' => ['ouvert' => false],
                'samedi'   => ['ouvert' => false],
                'dimanche' => ['ouvert' => false],
            ];

            expect(fn () => HorairesOuverture::fromArray($data))
                ->toThrow(InvalidArgumentException::class, 'au moins un jour par semaine');
        });

    });

});
