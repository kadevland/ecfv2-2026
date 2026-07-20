<?php

declare(strict_types=1);

use App\Domain\User\Entities\UserProfil;
use App\Domain\User\ValueObjects\UserProfilId;

describe('UserProfil Entity Basic', function () {

    it('peut créer un profil utilisateur', function () {
        $profil = UserProfil::create(
            nom: 'Dupond',
            prenom: 'Jacques',
            email: 'jacques@test.fr'
        );

        expect($profil->id)->toBeInstanceOf(UserProfilId::class);
        expect($profil->nom)->toBe('Dupond');
        expect($profil->prenom)->toBe('Jacques');
        expect($profil->email)->toBe('jacques@test.fr');
    });

    it('peut changer les informations', function () {
        $profil = UserProfil::create(
            nom: 'Ancien',
            prenom: 'Nom',
            email: 'ancien@test.fr'
        );

        $profil->changerNom('Nouveau');
        $profil->changerPrenom('Prenom');

        expect($profil->nom)->toBe('Nouveau');
        expect($profil->prenom)->toBe('Prenom');
    });

    it('est actif par défaut', function () {
        $profil = UserProfil::create(
            nom: 'Test',
            prenom: 'User',
            email: 'test@example.com'
        );

        expect($profil->isActif())->toBeTrue();
    });
});
