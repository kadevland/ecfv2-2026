<?php

declare(strict_types=1);

use App\Application\Film\Commands\CreateFilm\CreateFilmCommand;

describe('CreateFilmCommand', function () {
    describe('Validation', function () {
        it('valide des données correctes', function () {
            $command = new CreateFilmCommand(
                titre: 'Film Test',
                realisateurs: ['Réalisateur Test'],
                genres: ['Action', 'Drame'],
                dureeMinutes: 120,
                classification: 'TOUS_PUBLICS',
                dateSortie: '2024-12-01'
            );

            $errors = $command->validate();

            expect($errors)->toBeEmpty();
            expect($command->isValid())->toBeTrue();
        });

        it('rejette un titre vide', function () {
            $command = new CreateFilmCommand(
                titre: '',
                realisateurs: ['Réalisateur Test'],
                genres: ['Action'],
                dureeMinutes: 120,
                classification: 'TOUS_PUBLICS',
                dateSortie: '2024-12-01'
            );

            $errors = $command->validate();

            expect($errors)->toHaveKey('titre');
            expect($command->isValid())->toBeFalse();
        });

        it('rejette un titre trop long', function () {
            $command = new CreateFilmCommand(
                titre: str_repeat('A', 201),
                realisateurs: ['Réalisateur Test'],
                genres: ['Action'],
                dureeMinutes: 120,
                classification: 'TOUS_PUBLICS',
                dateSortie: '2024-12-01'
            );

            $errors = $command->validate();

            expect($errors)->toHaveKey('titre');
            expect($command->isValid())->toBeFalse();
        });

        it('rejette des réalisateurs vides', function () {
            $command = new CreateFilmCommand(
                titre: 'Film Test',
                realisateurs: [],
                genres: ['Action'],
                dureeMinutes: 120,
                classification: 'TOUS_PUBLICS',
                dateSortie: '2024-12-01'
            );

            $errors = $command->validate();

            expect($errors)->toHaveKey('realisateurs');
            expect($command->isValid())->toBeFalse();
        });

        it('rejette des genres vides', function () {
            $command = new CreateFilmCommand(
                titre: 'Film Test',
                realisateurs: ['Réalisateur Test'],
                genres: [],
                dureeMinutes: 120,
                classification: 'TOUS_PUBLICS',
                dateSortie: '2024-12-01'
            );

            $errors = $command->validate();

            expect($errors)->toHaveKey('genres');
            expect($command->isValid())->toBeFalse();
        });

        it('rejette une durée invalide', function () {
            $command = new CreateFilmCommand(
                titre: 'Film Test',
                realisateurs: ['Réalisateur Test'],
                genres: ['Action'],
                dureeMinutes: 0, // Durée invalide
                classification: 'TOUS_PUBLICS',
                dateSortie: '2024-12-01'
            );

            $errors = $command->validate();

            expect($errors)->toHaveKey('dureeMinutes');
            expect($command->isValid())->toBeFalse();
        });

        it('rejette une classification invalide', function () {
            $command = new CreateFilmCommand(
                titre: 'Film Test',
                realisateurs: ['Réalisateur Test'],
                genres: ['Action'],
                dureeMinutes: 120,
                classification: 'INVALID', // Classification invalide
                dateSortie: '2024-12-01'
            );

            $errors = $command->validate();

            expect($errors)->toHaveKey('classification');
            expect($command->isValid())->toBeFalse();
        });

        it('rejette une date de sortie invalide', function () {
            $command = new CreateFilmCommand(
                titre: 'Film Test',
                realisateurs: ['Réalisateur Test'],
                genres: ['Action'],
                dureeMinutes: 120,
                classification: 'TOUS_PUBLICS',
                dateSortie: '2024-13-01' // Date invalide
            );

            $errors = $command->validate();

            expect($errors)->toHaveKey('dateSortie');
            expect($command->isValid())->toBeFalse();
        });

        it('valide des notes optionnelles correctes', function () {
            $command = new CreateFilmCommand(
                titre: 'Film Test',
                realisateurs: ['Réalisateur Test'],
                genres: ['Action'],
                dureeMinutes: 120,
                classification: 'TOUS_PUBLICS',
                dateSortie: '2024-12-01',
                notePresse: 7.5,
                notePublic: 8.0
            );

            $errors = $command->validate();

            expect($errors)->toBeEmpty();
            expect($command->isValid())->toBeTrue();
        });

        it('rejette des notes hors limites', function () {
            $command = new CreateFilmCommand(
                titre: 'Film Test',
                realisateurs: ['Réalisateur Test'],
                genres: ['Action'],
                dureeMinutes: 120,
                classification: 'TOUS_PUBLICS',
                dateSortie: '2024-12-01',
                notePresse: 11.0, // > 10
                notePublic: -1.0  // < 0
            );

            $errors = $command->validate();

            expect($errors)->toHaveKey('notePresse');
            expect($errors)->toHaveKey('notePublic');
            expect($command->isValid())->toBeFalse();
        });

        it('valide des URLs optionnelles correctes', function () {
            $command = new CreateFilmCommand(
                titre: 'Film Test',
                realisateurs: ['Réalisateur Test'],
                genres: ['Action'],
                dureeMinutes: 120,
                classification: 'TOUS_PUBLICS',
                dateSortie: '2024-12-01',
                afficheUrl: 'https://example.com/affiche.jpg',
                bandeAnnonceUrl: 'https://example.com/trailer.mp4'
            );

            $errors = $command->validate();

            expect($errors)->toBeEmpty();
            expect($command->isValid())->toBeTrue();
        });

        it('rejette des URLs invalides', function () {
            $command = new CreateFilmCommand(
                titre: 'Film Test',
                realisateurs: ['Réalisateur Test'],
                genres: ['Action'],
                dureeMinutes: 120,
                classification: 'TOUS_PUBLICS',
                dateSortie: '2024-12-01',
                afficheUrl: 'invalid-url',
                bandeAnnonceUrl: 'also-invalid'
            );

            $errors = $command->validate();

            expect($errors)->toHaveKey('afficheUrl');
            expect($errors)->toHaveKey('bandeAnnonceUrl');
            expect($command->isValid())->toBeFalse();
        });
    });

    describe('Propriétés', function () {
        it('stocke toutes les propriétés correctement', function () {
            $command = new CreateFilmCommand(
                titre: 'Film Complet',
                realisateurs: ['Christopher Nolan', 'Denis Villeneuve'],
                genres: ['Science-Fiction', 'Thriller'],
                dureeMinutes: 148,
                classification: 'TOUS_PUBLICS',
                dateSortie: '2024-07-15',
                titreFr: 'Titre Français',
                acteursPrincipaux: ['Actor 1', 'Actor 2'],
                langueOriginale: 'en',
                sousTitres: 'fr',
                resume: 'Un résumé du film...',
                dateFinExploitation: '2024-12-31',
                notePresse: 8.5,
                notePublic: 7.8,
                afficheUrl: 'https://example.com/poster.jpg',
                bandeAnnonceUrl: 'https://example.com/trailer.mp4',
                estActif: false
            );

            expect($command->titre)->toBe('Film Complet');
            expect($command->realisateurs)->toBe(['Christopher Nolan', 'Denis Villeneuve']);
            expect($command->genres)->toBe(['Science-Fiction', 'Thriller']);
            expect($command->dureeMinutes)->toBe(148);
            expect($command->classification)->toBe('TOUS_PUBLICS');
            expect($command->dateSortie)->toBe('2024-07-15');
            expect($command->titreFr)->toBe('Titre Français');
            expect($command->acteursPrincipaux)->toBe(['Actor 1', 'Actor 2']);
            expect($command->langueOriginale)->toBe('en');
            expect($command->sousTitres)->toBe('fr');
            expect($command->resume)->toBe('Un résumé du film...');
            expect($command->dateFinExploitation)->toBe('2024-12-31');
            expect($command->notePresse)->toBe(8.5);
            expect($command->notePublic)->toBe(7.8);
            expect($command->afficheUrl)->toBe('https://example.com/poster.jpg');
            expect($command->bandeAnnonceUrl)->toBe('https://example.com/trailer.mp4');
            expect($command->estActif)->toBeFalse();
        });

        it('utilise des valeurs par défaut pour les propriétés optionnelles', function () {
            $command = new CreateFilmCommand(
                titre: 'Film Minimal',
                realisateurs: ['Réalisateur Minimal'],
                genres: ['Drame'],
                dureeMinutes: 90,
                classification: 'TOUS_PUBLICS',
                dateSortie: '2024-06-01'
            );

            expect($command->titreFr)->toBeNull();
            expect($command->acteursPrincipaux)->toBe([]);
            expect($command->langueOriginale)->toBeNull();
            expect($command->sousTitres)->toBeNull();
            expect($command->resume)->toBeNull();
            expect($command->dateFinExploitation)->toBeNull();
            expect($command->notePresse)->toBeNull();
            expect($command->notePublic)->toBeNull();
            expect($command->afficheUrl)->toBeNull();
            expect($command->bandeAnnonceUrl)->toBeNull();
            expect($command->estActif)->toBeTrue(); // Par défaut
        });
    });
});
