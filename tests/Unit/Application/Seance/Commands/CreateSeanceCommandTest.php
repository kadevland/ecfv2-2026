<?php

declare(strict_types=1);

use App\Application\Seance\Commands\CreateSeance\CreateSeanceCommand;

describe('CreateSeanceCommand', function () {
    describe('Validation', function () {
        it('valide des données correctes', function () {
            $command = new CreateSeanceCommand(
                filmUuid: '01933b4e-8f85-7000-8000-000000000000',
                salleUuid: '01933b4e-8f85-7001-8000-000000000001',
                dateHeureDebut: '2024-12-01 20:00:00',
                dateHeureFin: '2024-12-01 22:30:00',
                version: 'vf',
                tarifsBase: ['normal' => 12.50, 'reduit' => 8.00],
                tauxTva: 20.0,
                statut: 'PROGRAMMEE'
            );

            $errors = $command->validate();

            expect($errors)->toBeEmpty();
            expect($command->isValid())->toBeTrue();
        });

        it('rejette un UUID film invalide', function () {
            $command = new CreateSeanceCommand(
                filmUuid: 'invalid-uuid',
                salleUuid: '550e8400-e29b-41d4-a716-446655440001',
                dateHeureDebut: '2024-12-01 20:00:00',
                dateHeureFin: '2024-12-01 22:30:00',
                version: 'vf',
                tarifsBase: ['normal' => 12.50],
                tauxTva: 20.0
            );

            $errors = $command->validate();

            expect($errors)->toHaveKey('filmUuid');
            expect($command->isValid())->toBeFalse();
        });

        it('rejette un UUID salle invalide', function () {
            $command = new CreateSeanceCommand(
                filmUuid: '550e8400-e29b-41d4-a716-446655440000',
                salleUuid: 'invalid-uuid',
                dateHeureDebut: '2024-12-01 20:00:00',
                dateHeureFin: '2024-12-01 22:30:00',
                version: 'vf',
                tarifsBase: ['normal' => 12.50],
                tauxTva: 20.0
            );

            $errors = $command->validate();

            expect($errors)->toHaveKey('salleUuid');
            expect($command->isValid())->toBeFalse();
        });

        it('rejette une date de fin antérieure à la date de début', function () {
            $command = new CreateSeanceCommand(
                filmUuid: '01933b4e-8f85-7000-8000-000000000000',
                salleUuid: '01933b4e-8f85-7001-8000-000000000001',
                dateHeureDebut: '2024-12-01 20:00:00',
                dateHeureFin: '2024-12-01 19:00:00', // Antérieure au début
                version: 'vf',
                tarifsBase: ['normal' => 12.50],
                tauxTva: 20.0
            );

            $errors = $command->validate();

            expect($errors)->toHaveKey('dateHeureFin');
            expect($command->isValid())->toBeFalse();
        });

        it('rejette une version invalide', function () {
            $command = new CreateSeanceCommand(
                filmUuid: '01933b4e-8f85-7000-8000-000000000000',
                salleUuid: '01933b4e-8f85-7001-8000-000000000001',
                dateHeureDebut: '2024-12-01 20:00:00',
                dateHeureFin: '2024-12-01 22:30:00',
                version: 'invalid', // Version invalide
                tarifsBase: ['normal' => 12.50],
                tauxTva: 20.0
            );

            $errors = $command->validate();

            expect($errors)->toHaveKey('version');
            expect($command->isValid())->toBeFalse();
        });

        it('rejette des tarifs de base vides', function () {
            $command = new CreateSeanceCommand(
                filmUuid: '01933b4e-8f85-7000-8000-000000000000',
                salleUuid: '01933b4e-8f85-7001-8000-000000000001',
                dateHeureDebut: '2024-12-01 20:00:00',
                dateHeureFin: '2024-12-01 22:30:00',
                version: 'vf',
                tarifsBase: [], // Tarifs vides
                tauxTva: 20.0
            );

            $errors = $command->validate();

            expect($errors)->toHaveKey('tarifsBase');
            expect($command->isValid())->toBeFalse();
        });

        it('rejette un taux TVA invalide', function () {
            $command = new CreateSeanceCommand(
                filmUuid: '01933b4e-8f85-7000-8000-000000000000',
                salleUuid: '01933b4e-8f85-7001-8000-000000000001',
                dateHeureDebut: '2024-12-01 20:00:00',
                dateHeureFin: '2024-12-01 22:30:00',
                version: 'vf',
                tarifsBase: ['normal' => 12.50],
                tauxTva: 150.0 // Taux TVA invalide
            );

            $errors = $command->validate();

            expect($errors)->toHaveKey('tauxTva');
            expect($command->isValid())->toBeFalse();
        });

        it('rejette une devise non supportée', function () {
            $command = new CreateSeanceCommand(
                filmUuid: '01933b4e-8f85-7000-8000-000000000000',
                salleUuid: '01933b4e-8f85-7001-8000-000000000001',
                dateHeureDebut: '2024-12-01 20:00:00',
                dateHeureFin: '2024-12-01 22:30:00',
                version: 'vf',
                tarifsBase: ['normal' => 12.50],
                tauxTva: 20.0,
                devise: 'XXX' // Devise non supportée
            );

            $errors = $command->validate();

            expect($errors)->toHaveKey('devise');
            expect($command->isValid())->toBeFalse();
        });

        it('rejette un statut invalide', function () {
            $command = new CreateSeanceCommand(
                filmUuid: '01933b4e-8f85-7000-8000-000000000000',
                salleUuid: '01933b4e-8f85-7001-8000-000000000001',
                dateHeureDebut: '2024-12-01 20:00:00',
                dateHeureFin: '2024-12-01 22:30:00',
                version: 'vf',
                tarifsBase: ['normal' => 12.50],
                tauxTva: 20.0,
                statut: 'invalid_status' // Statut invalide
            );

            $errors = $command->validate();

            expect($errors)->toHaveKey('statut');
            expect($command->isValid())->toBeFalse();
        });

        it('valide des suppléments spéciaux corrects', function () {
            $command = new CreateSeanceCommand(
                filmUuid: '01933b4e-8f85-7000-8000-000000000000',
                salleUuid: '01933b4e-8f85-7001-8000-000000000001',
                dateHeureDebut: '2024-12-01 20:00:00',
                dateHeureFin: '2024-12-01 22:30:00',
                version: 'vf',
                tarifsBase: ['normal' => 12.50],
                tauxTva: 20.0,
                statut: 'PROGRAMMEE',
                supplementsSpeciaux: ['3d' => 3.00, 'imax' => 5.00]
            );

            $errors = $command->validate();

            expect($errors)->toBeEmpty();
            expect($command->isValid())->toBeTrue();
        });

        it('valide des réductions spéciales correctes', function () {
            $command = new CreateSeanceCommand(
                filmUuid: '01933b4e-8f85-7000-8000-000000000000',
                salleUuid: '01933b4e-8f85-7001-8000-000000000001',
                dateHeureDebut: '2024-12-01 20:00:00',
                dateHeureFin: '2024-12-01 22:30:00',
                version: 'vf',
                tarifsBase: ['normal' => 12.50],
                tauxTva: 20.0,
                statut: 'PROGRAMMEE',
                reductionsSpeciales: ['etudiant' => 2.00, 'groupe' => 1.50]
            );

            $errors = $command->validate();

            expect($errors)->toBeEmpty();
            expect($command->isValid())->toBeTrue();
        });
    });

    describe('Propriétés', function () {
        it('stocke toutes les propriétés correctement', function () {
            $command = new CreateSeanceCommand(
                filmUuid: '01933b4e-8f85-7000-8000-000000000000',
                salleUuid: '01933b4e-8f85-7001-8000-000000000001',
                dateHeureDebut: '2024-12-15 21:00:00',
                dateHeureFin: '2024-12-15 23:30:00',
                version: 'vostfr',
                tarifsBase: ['normal' => 14.00, 'reduit' => 9.50, 'enfant' => 7.00],
                tauxTva: 5.5,
                devise: 'CHF',
                placementLibre: true,
                statut: 'EN_COURS',
                optionsSupplementaires: ['accessibility' => true],
                supplementsSpeciaux: ['dolby_atmos' => 2.50],
                reductionsSpeciales: ['senior' => 3.00]
            );

            expect($command->filmUuid)->toBe('01933b4e-8f85-7000-8000-000000000000');
            expect($command->salleUuid)->toBe('01933b4e-8f85-7001-8000-000000000001');
            expect($command->dateHeureDebut)->toBe('2024-12-15 21:00:00');
            expect($command->dateHeureFin)->toBe('2024-12-15 23:30:00');
            expect($command->version)->toBe('vostfr');
            expect($command->tarifsBase)->toBe(['normal' => 14.00, 'reduit' => 9.50, 'enfant' => 7.00]);
            expect($command->tauxTva)->toBe(5.5);
            expect($command->devise)->toBe('CHF');
            expect($command->placementLibre)->toBeTrue();
            expect($command->statut)->toBe('EN_COURS');
            expect($command->optionsSupplementaires)->toBe(['accessibility' => true]);
            expect($command->supplementsSpeciaux)->toBe(['dolby_atmos' => 2.50]);
            expect($command->reductionsSpeciales)->toBe(['senior' => 3.00]);
        });

        it('utilise des valeurs par défaut pour les propriétés optionnelles', function () {
            $command = new CreateSeanceCommand(
                filmUuid: '01933b4e-8f85-7000-8000-000000000000',
                salleUuid: '01933b4e-8f85-7001-8000-000000000001',
                dateHeureDebut: '2024-12-01 20:00:00',
                dateHeureFin: '2024-12-01 22:30:00',
                version: 'vf',
                tarifsBase: ['normal' => 12.50],
                tauxTva: 20.0
            );

            expect($command->devise)->toBe('EUR');
            expect($command->placementLibre)->toBeFalse();
            expect($command->statut)->toBe('programmee');
            expect($command->optionsSupplementaires)->toBeNull();
            expect($command->supplementsSpeciaux)->toBeNull();
            expect($command->reductionsSpeciales)->toBeNull();
        });
    });
});
