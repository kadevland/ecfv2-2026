<?php

declare(strict_types=1);

use App\Application\Cinema\Commands\CreateCinema\CreateCinemaCommand;

describe('CreateCinemaCommand', function () {
    describe('Validation', function () {
        it('valide des données correctes', function () {
            $command = new CreateCinemaCommand(
                nom: 'Cinéma Test',
                rue: '123 Rue du Test',
                ville: 'Paris',
                codePostal: '75001',
                pays: 'FR',
                latitude: 48.8566,
                longitude: 2.3522
            );

            $errors = $command->validate();

            expect($errors)->toBeEmpty();
            expect($command->isValid())->toBeTrue();
        });

        it('rejette un nom trop court', function () {
            $command = new CreateCinemaCommand(
                nom: 'A', // Trop court
                rue: '123 Rue du Test',
                ville: 'Paris',
                codePostal: '75001',
                pays: 'FR',
                latitude: 48.8566,
                longitude: 2.3522
            );

            $errors = $command->validate();

            expect($errors)->toContain('Le nom doit contenir entre 2 et 100 caractères');
            expect($command->isValid())->toBeFalse();
        });

        it('rejette un nom trop long', function () {
            $command = new CreateCinemaCommand(
                nom: str_repeat('A', 101), // Trop long
                rue: '123 Rue du Test',
                ville: 'Paris',
                codePostal: '75001',
                pays: 'FR',
                latitude: 48.8566,
                longitude: 2.3522
            );

            $errors = $command->validate();

            expect($errors)->toContain('Le nom doit contenir entre 2 et 100 caractères');
            expect($command->isValid())->toBeFalse();
        });

        it('rejette une adresse trop courte', function () {
            $command = new CreateCinemaCommand(
                nom: 'Cinéma Test',
                rue: '123', // Trop court
                ville: 'Paris',
                codePostal: '75001',
                pays: 'FR',
                latitude: 48.8566,
                longitude: 2.3522
            );

            $errors = $command->validate();

            expect($errors)->toContain('L\'adresse doit contenir entre 5 et 200 caractères');
            expect($command->isValid())->toBeFalse();
        });

        it('rejette un code pays invalide', function () {
            $command = new CreateCinemaCommand(
                nom: 'Cinéma Test',
                rue: '123 Rue du Test',
                ville: 'Paris',
                codePostal: '75001',
                pays: 'XXX', // Code pays invalide
                latitude: 48.8566,
                longitude: 2.3522
            );

            $errors = $command->validate();

            expect($errors)->toContain('Le code pays doit être un code ISO de 2 caractères');
            expect($command->isValid())->toBeFalse();
        });

        it('rejette des coordonnées GPS invalides', function () {
            $command = new CreateCinemaCommand(
                nom: 'Cinéma Test',
                rue: '123 Rue du Test',
                ville: 'Paris',
                codePostal: '75001',
                pays: 'FR',
                latitude: 95.0, // Latitude invalide
                longitude: 200.0 // Longitude invalide
            );

            $errors = $command->validate();

            expect($errors)->toContain('La latitude doit être comprise entre -90 et 90');
            expect($errors)->toContain('La longitude doit être comprise entre -180 et 180');
            expect($command->isValid())->toBeFalse();
        });

        it('valide un email optionnel correct', function () {
            $command = new CreateCinemaCommand(
                nom: 'Cinéma Test',
                rue: '123 Rue du Test',
                ville: 'Paris',
                codePostal: '75001',
                pays: 'FR',
                latitude: 48.8566,
                longitude: 2.3522,
                email: 'contact@cinema.fr'
            );

            $errors = $command->validate();

            expect($errors)->toBeEmpty();
            expect($command->isValid())->toBeTrue();
        });

        it('rejette un email invalide', function () {
            $command = new CreateCinemaCommand(
                nom: 'Cinéma Test',
                rue: '123 Rue du Test',
                ville: 'Paris',
                codePostal: '75001',
                pays: 'FR',
                latitude: 48.8566,
                longitude: 2.3522,
                email: 'email-invalide'
            );

            $errors = $command->validate();

            expect($errors)->toContain('L\'adresse email n\'est pas valide');
            expect($command->isValid())->toBeFalse();
        });

        it('valide une description optionnelle correcte', function () {
            $command = new CreateCinemaCommand(
                nom: 'Cinéma Test',
                rue: '123 Rue du Test',
                ville: 'Paris',
                codePostal: '75001',
                pays: 'FR',
                latitude: 48.8566,
                longitude: 2.3522,
                description: 'Un cinéma moderne avec écran IMAX'
            );

            $errors = $command->validate();

            expect($errors)->toBeEmpty();
            expect($command->isValid())->toBeTrue();
        });

        it('rejette une description trop longue', function () {
            $command = new CreateCinemaCommand(
                nom: 'Cinéma Test',
                rue: '123 Rue du Test',
                ville: 'Paris',
                codePostal: '75001',
                pays: 'FR',
                latitude: 48.8566,
                longitude: 2.3522,
                description: str_repeat('A', 1001) // Trop long
            );

            $errors = $command->validate();

            expect($errors)->toContain('La description ne peut pas dépasser 1000 caractères');
            expect($command->isValid())->toBeFalse();
        });
    });

    describe('Propriétés', function () {
        it('stocke toutes les propriétés correctement', function () {
            $command = new CreateCinemaCommand(
                nom: 'Cinéma Complet',
                rue: '456 Avenue Test',
                ville: 'Lyon',
                codePostal: '69000',
                pays: 'FR',
                latitude: 45.7640,
                longitude: 4.8357,
                telephone: '+33412345678',
                email: 'lyon@cinema.fr',
                description: 'Un cinéma moderne à Lyon',
                estActif: false
            );

            expect($command->nom)->toBe('Cinéma Complet');
            expect($command->rue)->toBe('456 Avenue Test');
            expect($command->ville)->toBe('Lyon');
            expect($command->codePostal)->toBe('69000');
            expect($command->pays)->toBe('FR');
            expect($command->latitude)->toBe(45.7640);
            expect($command->longitude)->toBe(4.8357);
            expect($command->telephone)->toBe('+33412345678');
            expect($command->email)->toBe('lyon@cinema.fr');
            expect($command->description)->toBe('Un cinéma moderne à Lyon');
            expect($command->estActif)->toBeFalse();
        });

        it('utilise des valeurs par défaut pour les propriétés optionnelles', function () {
            $command = new CreateCinemaCommand(
                nom: 'Cinéma Minimal',
                rue: '789 Rue Minimal',
                ville: 'Marseille',
                codePostal: '13000',
                pays: 'FR',
                latitude: 43.2965,
                longitude: 5.3698
            );

            expect($command->telephone)->toBeNull();
            expect($command->email)->toBeNull();
            expect($command->description)->toBeNull();
            expect($command->estActif)->toBeTrue(); // Par défaut
        });
    });
});
