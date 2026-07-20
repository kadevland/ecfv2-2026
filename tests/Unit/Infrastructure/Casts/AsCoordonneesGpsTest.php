<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Casts\AsCoordonneesGps;
use App\Domain\Shared\ValueObjects\CoordonneesGps;

describe('AsCoordonneesGps Cast', function () {
    beforeEach(function () {
        $this->cast  = new AsCoordonneesGps;
        $this->model = new class extends Model {};
    });

    describe('get() - Hydration depuis DB JSONB', function () {
        it('retourne null pour valeur null', function () {
            $result = $this->cast->get($this->model, 'coordonnees_gps', null, []);
            expect($result)->toBeNull();
        });

        it('retourne null pour JSON invalide', function () {
            $result = $this->cast->get($this->model, 'coordonnees_gps', 'invalid-json{', []);
            expect($result)->toBeNull();
        });

        it('retourne null pour JSON valide mais données GPS invalides', function () {
            $invalidData = json_encode(['latitude' => 48.8566]); // longitude manquante
            $result      = $this->cast->get($this->model, 'coordonnees_gps', $invalidData, []);
            expect($result)->toBeNull();
        });

        it('retourne CoordonneesGps pour JSON valide avec données complètes', function () {
            $jsonData = json_encode([
                'latitude'  => 48.8566,
                'longitude' => 2.3522,
            ]);

            $result = $this->cast->get($this->model, 'coordonnees_gps', $jsonData, []);

            expect($result)->toBeInstanceOf(CoordonneesGps::class);
            expect($result->latitude)->toBe(48.8566);
            expect($result->longitude)->toBe(2.3522);
        });

        it('gère différents formats de coordonnées réalistes', function () {
            // Paris (Tour Eiffel)
            $parisCoords = json_encode([
                'latitude'  => 48.8566,
                'longitude' => 2.3522,
            ]);

            // Londres (Big Ben)
            $londresCoords = json_encode([
                'latitude'  => 51.5007,
                'longitude' => -0.1246,
            ]);

            // Bruxelles (Grand Place)
            $bruxellesCoords = json_encode([
                'latitude'  => 50.8503,
                'longitude' => 4.3517,
            ]);

            $parisResult     = $this->cast->get($this->model, 'coordonnees_gps', $parisCoords, []);
            $londresResult   = $this->cast->get($this->model, 'coordonnees_gps', $londresCoords, []);
            $bruxellesResult = $this->cast->get($this->model, 'coordonnees_gps', $bruxellesCoords, []);

            expect($parisResult)->toBeInstanceOf(CoordonneesGps::class);
            expect($parisResult->latitude)->toBe(48.8566);

            expect($londresResult)->toBeInstanceOf(CoordonneesGps::class);
            expect($londresResult->longitude)->toBe(-0.1246);

            expect($bruxellesResult)->toBeInstanceOf(CoordonneesGps::class);
            expect($bruxellesResult->latitude)->toBe(50.8503);
        });

        it('rejette les coordonnées hors limites géographiques', function () {
            // Latitude trop élevée
            $invalidLatitude = json_encode([
                'latitude'  => 91.0,
                'longitude' => 2.3522,
            ]);

            // Longitude trop faible
            $invalidLongitude = json_encode([
                'latitude'  => 48.8566,
                'longitude' => -181.0,
            ]);

            $result1 = $this->cast->get($this->model, 'coordonnees_gps', $invalidLatitude, []);
            $result2 = $this->cast->get($this->model, 'coordonnees_gps', $invalidLongitude, []);

            expect($result1)->toBeNull();
            expect($result2)->toBeNull();
        });
    });

    describe('set() - Persistence vers DB JSONB', function () {
        it('retourne null pour valeur null', function () {
            $result = $this->cast->set($this->model, 'coordonnees_gps', null, []);
            expect($result)->toBeNull();
        });

        it('convertit CoordonneesGps VO vers JSON', function () {
            $coords = CoordonneesGps::creer(48.8566, 2.3522);

            $result  = $this->cast->set($this->model, 'coordonnees_gps', $coords, []);
            $decoded = json_decode($result, true);

            expect($decoded)->toBe([
                'latitude'  => 48.8566,
                'longitude' => 2.3522,
            ]);
        });

        it('convertit array vers JSON via CoordonneesGps VO', function () {
            $data = [
                'latitude'  => 48.8566,
                'longitude' => 2.3522,
            ];

            $result  = $this->cast->set($this->model, 'coordonnees_gps', $data, []);
            $decoded = json_decode($result, true);

            expect($decoded)->toBe($data);
        });

        it('retourne null pour array invalide', function () {
            $invalidData = ['latitude' => 48.8566]; // longitude manquante

            $result = $this->cast->set($this->model, 'coordonnees_gps', $invalidData, []);
            expect($result)->toBeNull();
        });

        it('retourne null pour coordonnées hors limites', function () {
            $invalidData = [
                'latitude'  => 91.0, // invalide
                'longitude' => 2.3522,
            ];

            $result = $this->cast->set($this->model, 'coordonnees_gps', $invalidData, []);
            expect($result)->toBeNull();
        });

        it('gère les coordonnées limites valides', function () {
            $limitCoords = [
                'latitude'  => 90.0,    // limite max valide
                'longitude' => -180.0,  // limite min valide
            ];

            $result  = $this->cast->set($this->model, 'coordonnees_gps', $limitCoords, []);
            $decoded = json_decode($result, true);

            // JSON ne préserve pas les types float/int exactement, on vérifie l'égalité numérique
            expect($decoded['latitude'])->toEqual(90.0);
            expect($decoded['longitude'])->toEqual(-180.0);
        });

        it('retourne null pour type non supporté', function () {
            $result1 = $this->cast->set($this->model, 'coordonnees_gps', 'string', []);
            $result2 = $this->cast->set($this->model, 'coordonnees_gps', 123, []);
            $result3 = $this->cast->set($this->model, 'coordonnees_gps', true, []);

            expect($result1)->toBeNull();
            expect($result2)->toBeNull();
            expect($result3)->toBeNull();
        });
    });

    describe('Cycle complet DB JSONB', function () {
        it('round-trip: set puis get conserve la valeur', function () {
            $originalCoords = CoordonneesGps::creer(48.8566, 2.3522);

            // Simule sauvegarde en DB JSONB
            $jsonValue = $this->cast->set($this->model, 'coordonnees_gps', $originalCoords, []);

            // Simule lecture depuis DB JSONB
            $retrievedCoords = $this->cast->get($this->model, 'coordonnees_gps', $jsonValue, []);

            expect($retrievedCoords)->toBeInstanceOf(CoordonneesGps::class);
            expect($retrievedCoords->equals($originalCoords))->toBeTrue();
        });

        it('round-trip avec array input', function () {
            $coordsData = [
                'latitude'  => 51.5007,
                'longitude' => -0.1246,
            ];

            // Simule sauvegarde array → JSONB
            $jsonValue = $this->cast->set($this->model, 'coordonnees_gps', $coordsData, []);

            // Simule lecture JSONB → CoordonneesGps VO
            $retrievedCoords = $this->cast->get($this->model, 'coordonnees_gps', $jsonValue, []);

            expect($retrievedCoords)->toBeInstanceOf(CoordonneesGps::class);
            expect($retrievedCoords->latitude)->toBe(51.5007);
            expect($retrievedCoords->longitude)->toBe(-0.1246);
        });

        it('round-trip avec coordonnées de cinémas réalistes', function () {
            // UGC Ciné Cité Paris 19
            $ugcParisCoords = [
                'latitude'  => 48.8944,
                'longitude' => 2.3851,
            ];

            // Kinepolis Bruxelles
            $kinepolisBxlCoords = [
                'latitude'  => 50.8947,
                'longitude' => 4.3308,
            ];

            // Test UGC Paris
            $jsonValue1       = $this->cast->set($this->model, 'coordonnees_gps', $ugcParisCoords, []);
            $retrievedCoords1 = $this->cast->get($this->model, 'coordonnees_gps', $jsonValue1, []);

            expect($retrievedCoords1->isInFranceMetropolitaine())->toBeTrue();

            // Test Kinepolis Bruxelles
            $jsonValue2       = $this->cast->set($this->model, 'coordonnees_gps', $kinepolisBxlCoords, []);
            $retrievedCoords2 = $this->cast->get($this->model, 'coordonnees_gps', $jsonValue2, []);

            expect($retrievedCoords2->isInBelgique())->toBeTrue();

            // Distance entre les deux
            $distance = $retrievedCoords1->distanceVers($retrievedCoords2);
            expect($distance)->toBeGreaterThan(250); // Plus de 250km
            expect($distance)->toBeLessThan(350); // Moins de 350km
        });
    });
});
