<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Casts\CinemaIdCast;
use App\Domain\Cinema\ValueObjects\CinemaId;

describe('CinemaIdCast', function () {
    beforeEach(function () {
        $this->cast      = new CinemaIdCast;
        $this->model     = new class extends Model {};
        $this->validUuid = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
    });

    describe('get() - Hydration depuis DB', function () {
        it('retourne null pour valeur null', function () {
            $result = $this->cast->get($this->model, 'id', null, []);
            expect($result)->toBeNull();
        });

        it('retourne null pour string vide', function () {
            $result = $this->cast->get($this->model, 'id', '', []);
            expect($result)->toBeNull();
        });

        it('retourne CinemaId pour UUID valide', function () {
            $result = $this->cast->get($this->model, 'id', $this->validUuid, []);

            expect($result)->toBeInstanceOf(CinemaId::class);
            expect($result->value)->toBe($this->validUuid);
        });

        it('lève exception pour UUID invalide', function () {
            expect(fn () => $this->cast->get($this->model, 'id', 'invalid-uuid', []))
                ->toThrow(InvalidArgumentException::class);
        });

        it('gère différents formats UUID valides', function () {
            $uuidFormats = [
                'f47ac10b-58cc-4372-a567-0e02b2c3d479', // standard
                'F47AC10B-58CC-4372-A567-0E02B2C3D479', // uppercase
            ];

            foreach ($uuidFormats as $uuid) {
                $result = $this->cast->get($this->model, 'id', $uuid, []);
                expect($result)->toBeInstanceOf(CinemaId::class);
                expect($result->value)->toBe($uuid);
            }
        });
    });

    describe('set() - Persistence vers DB', function () {
        it('convertit CinemaId VO vers string UUID', function () {
            $cinemaId = CinemaId::fromString($this->validUuid);
            $result   = $this->cast->set($this->model, 'id', $cinemaId, []);

            expect($result)->toBe($this->validUuid);
        });

        it('convertit string UUID vers string UUID', function () {
            $result = $this->cast->set($this->model, 'id', $this->validUuid, []);
            expect($result)->toBe($this->validUuid);
        });

        it('retourne null pour valeur null', function () {
            $result = $this->cast->set($this->model, 'id', null, []);
            expect($result)->toBeNull();
        });

        it('lève exception pour string UUID invalide', function () {
            expect(fn () => $this->cast->set($this->model, 'id', 'invalid-uuid', []))
                ->toThrow(InvalidArgumentException::class);
        });

        it('valide et convertit UUID avec casse différente', function () {
            $uppercaseUuid = 'F47AC10B-58CC-4372-A567-0E02B2C3D479';
            $result        = $this->cast->set($this->model, 'id', $uppercaseUuid, []);
            expect($result)->toBe($uppercaseUuid);
        });
    });

    describe('Comportement Identity spécifique', function () {
        it('génère des IDs uniques', function () {
            $id1 = CinemaId::generate();
            $id2 = CinemaId::generate();

            expect($id1->value)->not->toBe($id2->value);

            // Test cast avec IDs générés
            $result1 = $this->cast->set($this->model, 'id', $id1, []);
            $result2 = $this->cast->set($this->model, 'id', $id2, []);

            expect($result1)->not->toBe($result2);
        });

        it('conserve l\'égalité d\'identité', function () {
            $uuid = $this->validUuid;
            $id1  = CinemaId::fromString($uuid);
            $id2  = CinemaId::fromString($uuid);

            expect($id1->equals($id2))->toBeTrue();

            // Test cast conserve l'égalité
            $result1 = $this->cast->set($this->model, 'id', $id1, []);
            $result2 = $this->cast->set($this->model, 'id', $id2, []);

            expect($result1)->toBe($result2);
        });
    });

    describe('Cycle complet DB', function () {
        it('round-trip: set puis get conserve la valeur', function () {
            $originalId = CinemaId::fromString($this->validUuid);

            // Simule sauvegarde en DB
            $dbValue = $this->cast->set($this->model, 'id', $originalId, []);

            // Simule lecture depuis DB
            $retrievedId = $this->cast->get($this->model, 'id', $dbValue, []);

            expect($retrievedId)->toBeInstanceOf(CinemaId::class);
            expect($retrievedId->equals($originalId))->toBeTrue();
        });

        it('round-trip avec ID généré', function () {
            $generatedId = CinemaId::generate();

            // Simule sauvegarde en DB
            $dbValue = $this->cast->set($this->model, 'id', $generatedId, []);

            // Simule lecture depuis DB
            $retrievedId = $this->cast->get($this->model, 'id', $dbValue, []);

            expect($retrievedId)->toBeInstanceOf(CinemaId::class);
            expect($retrievedId->equals($generatedId))->toBeTrue();
        });

        it('round-trip avec string UUID input', function () {
            // String UUID directement
            $dbValue     = $this->cast->set($this->model, 'id', $this->validUuid, []);
            $retrievedId = $this->cast->get($this->model, 'id', $dbValue, []);

            expect($retrievedId)->toBeInstanceOf(CinemaId::class);
            expect($retrievedId->value)->toBe($this->validUuid);
        });
    });
});
