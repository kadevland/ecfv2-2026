<?php

declare(strict_types=1);

use App\Domain\Employees\Entities\Incident;
use App\Domain\Employees\ValueObjects\IncidentId;

describe('Incident Entity Basic', function () {

    it('peut créer un incident', function () {
        $incident = Incident::create(
            titre: 'Panne projecteur',
            description: 'Le projecteur de la salle 1 ne fonctionne plus',
            priorite: 'HAUTE'
        );

        expect($incident->id)->toBeInstanceOf(IncidentId::class);
        expect($incident->titre)->toBe('Panne projecteur');
        expect($incident->priorite)->toBe('HAUTE');
    });

    it('est ouvert par défaut', function () {
        $incident = Incident::create(
            titre: 'Test',
            description: 'Description test',
            priorite: 'BASSE'
        );

        expect($incident->statut)->toBe('OUVERT');
    });

    it('peut être fermé', function () {
        $incident = Incident::create(
            titre: 'Test',
            description: 'Description',
            priorite: 'MOYENNE'
        );

        $incident->fermer('Problème résolu');

        expect($incident->statut)->toBe('FERME');
        expect($incident->resolution)->toBe('Problème résolu');
    });
});
