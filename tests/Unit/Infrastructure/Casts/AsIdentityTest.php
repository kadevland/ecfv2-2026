<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Casts\AsIdentity;
use App\Domain\Cinema\ValueObjects\CinemaId;

describe('AsIdentity Cast (générique)', function () {
    beforeEach(function () {
        $this->cast      = new AsIdentity(CinemaId::class);
        $this->model     = new class extends Model {};
        $this->validUuid = 'f47ac10b-58cc-4372-a567-0e02b2c3d479';
    });

    describe('get() - Hydration générique', function () {
        it('retourne null pour valeur null', function () {
            $result = $this->cast->get($this->model, 'id', null, []);
            expect($result)->toBeNull();
        });

        it('instancie la classe Identity configurée', function () {
            $result = $this->cast->get($this->model, 'id', $this->validUuid, []);

            expect($result)->toBeInstanceOf(CinemaId::class);
            expect($result->value)->toBe($this->validUuid);
        });

        it('peut être configuré pour différents types Identity', function () {
            // Test avec une autre classe hypothétique
            // (ici on teste juste le mécanisme générique)
            $genericCast = new AsIdentity(CinemaId::class);
            $result      = $genericCast->get($this->model, 'id', $this->validUuid, []);

            expect($result)->toBeInstanceOf(CinemaId::class);
        });
    });

    describe('set() - Persistence générique', function () {
        it('retourne null pour valeur null', function () {
            $result = $this->cast->set($this->model, 'id', null, []);
            expect($result)->toBeNull();
        });

        it('retourne string directement si déjà string', function () {
            $result = $this->cast->set($this->model, 'id', $this->validUuid, []);
            expect($result)->toBe($this->validUuid);
        });

        it('utilise méthode value() si disponible', function () {
            $cinemaId = CinemaId::fromString($this->validUuid);
            $result   = $this->cast->set($this->model, 'id', $cinemaId, []);

            expect($result)->toBe($this->validUuid);
        });

        it('utilise __toString si value() indisponible', function () {
            // Mock d'un objet avec __toString mais pas value()
            $mockObject = new class($this->validUuid)
            {
                public function __construct(private string $value) {}

                public function __toString(): string
                {
                    return $this->value;
                }
            };

            $result = $this->cast->set($this->model, 'id', $mockObject, []);
            expect($result)->toBe($this->validUuid);
        });

        it('cast en string en dernier recours', function () {
            $result = $this->cast->set($this->model, 'id', 123, []);
            expect($result)->toBe('123');
        });
    });

    describe('Polymorphisme Identity', function () {
        it('peut gérer différents types d\'Identity via configuration', function () {
            // Test concept polymorphique
            $cinemaIdCast = new AsIdentity(CinemaId::class);

            $id        = CinemaId::fromString($this->validUuid);
            $dbValue   = $cinemaIdCast->set($this->model, 'id', $id, []);
            $retrieved = $cinemaIdCast->get($this->model, 'id', $dbValue, []);

            expect($retrieved)->toBeInstanceOf(CinemaId::class);
            expect($retrieved->value)->toBe($this->validUuid);
        });
    });

    describe('Robustesse', function () {
        it('gère différents types d\'input gracieusement', function () {
            // String
            expect($this->cast->set($this->model, 'id', 'test', []))->toBe('test');

            // Numérique
            expect($this->cast->set($this->model, 'id', 42, []))->toBe('42');

            // Boolean
            expect($this->cast->set($this->model, 'id', true, []))->toBe('1');
            expect($this->cast->set($this->model, 'id', false, []))->toBe('');
        });
    });
});
