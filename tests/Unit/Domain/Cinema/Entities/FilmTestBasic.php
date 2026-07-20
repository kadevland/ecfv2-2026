<?php

declare(strict_types=1);

use App\Domain\Cinema\Entities\Film;
use App\Domain\Cinema\ValueObjects\FilmId;

describe('Film Entity Basic', function () {

    it('peut créer un film basique', function () {
        $film = Film::create(
            titre: 'Avatar',
            duree: 180,
            annee: 2023
        );

        expect($film->id)->toBeInstanceOf(FilmId::class);
        expect($film->titre)->toBe('Avatar');
        expect($film->duree)->toBe(180);
        expect($film->annee)->toBe(2023);
    });

    it('peut changer le titre', function () {
        $film = Film::create(
            titre: 'Titre Original',
            duree: 120,
            annee: 2023
        );

        $film->changerTitre('Nouveau Titre');
        expect($film->titre)->toBe('Nouveau Titre');
    });
});
