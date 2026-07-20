<?php

declare(strict_types=1);

use App\Domain\Employees\Entities\Employe;

describe('Employe Entity Basic', function () {

    it('peut créer un employé simple', function () {
        $employe = new Employe(
            nom: 'Dupont',
            prenom: 'Jean',
            email: 'jean.dupont@cinema.fr',
            poste: 'Projectionniste'
        );

        expect($employe->nom)->toBe('Dupont');
        expect($employe->prenom)->toBe('Jean');
        expect($employe->poste)->toBe('Projectionniste');
    });

    it('peut changer de poste', function () {
        $employe = new Employe(
            nom: 'Martin',
            prenom: 'Marie',
            email: 'marie@cinema.fr',
            poste: 'Caissier'
        );

        $employe->changerPoste('Manager');
        expect($employe->poste)->toBe('Manager');
    });

    it('est actif par défaut', function () {
        $employe = new Employe(
            nom: 'Test',
            prenom: 'Test',
            email: 'test@cinema.fr',
            poste: 'Test'
        );

        expect($employe->estActif())->toBeTrue();
    });
});
