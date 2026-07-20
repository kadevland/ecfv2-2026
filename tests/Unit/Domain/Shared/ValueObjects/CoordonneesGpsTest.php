<?php

declare(strict_types=1);

use App\Domain\Shared\ValueObjects\CoordonneesGps;

describe('CoordonneesGps ValueObject', function () {

    it('peut créer des coordonnées GPS', function () {
        $coords = CoordonneesGps::create(
            latitude: 48.8566,
            longitude: 2.3522
        );

        expect($coords->latitude)->toBe(48.8566);
        expect($coords->longitude)->toBe(2.3522);
    });

    it('peut créer des coordonnées négatives', function () {
        $coords = CoordonneesGps::create(
            latitude: -45.8566,
            longitude: -2.3522
        );

        expect($coords->latitude)->toBe(-45.8566);
        expect($coords->longitude)->toBe(-2.3522);
    });

    it('formate en string', function () {
        $coords = CoordonneesGps::create(
            latitude: 48.8566,
            longitude: 2.3522
        );

        $formatted = (string) $coords;
        expect($formatted)->toContain('48.8566');
        expect($formatted)->toContain('2.3522');
    });

    it('compare deux coordonnées', function () {
        $coords1 = CoordonneesGps::create(
            latitude: 48.8566,
            longitude: 2.3522
        );

        $coords2 = CoordonneesGps::create(
            latitude: 48.8566,
            longitude: 2.3522
        );

        expect($coords1->equals($coords2))->toBeTrue();
    });
});
