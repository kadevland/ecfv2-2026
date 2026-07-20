<?php

declare(strict_types=1);

use App\Domain\Cinema\Entities\Cinema;
use App\Domain\Cinema\ValueObjects\CinemaId;

describe('Cinema Entity Basic', function () {

    it('peut créer un cinéma basique', function () {
        $cinema = Cinema::create(
            nom: 'Cinéma Test',
            adresse: '123 Rue du Cinema',
            ville: 'Paris',
            codePostal: '75001'
        );

        expect($cinema->id)->toBeInstanceOf(CinemaId::class);
        expect($cinema->nom)->toBe('Cinéma Test');
        expect($cinema->adresse)->toBe('123 Rue du Cinema');
        expect($cinema->ville)->toBe('Paris');
    });

    it('peut changer le nom', function () {
        $cinema = Cinema::create(
            nom: 'Ancien Nom',
            adresse: '123 Rue Test',
            ville: 'Paris',
            codePostal: '75001'
        );

        $cinema->changerNom('Nouveau Nom');
        expect($cinema->nom)->toBe('Nouveau Nom');
    });
});
