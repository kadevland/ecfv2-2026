<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Casts\AsPhoneNumber;
use App\Domain\Shared\ValueObjects\PhoneNumber;

describe('AsPhoneNumber Cast', function () {
    beforeEach(function () {
        $this->cast  = new AsPhoneNumber;
        $this->model = new class extends Model {};
    });

    describe('get() - Hydration depuis DB', function () {
        it('retourne null pour valeur null', function () {
            $result = $this->cast->get($this->model, 'telephone', null, []);
            expect($result)->toBeNull();
        });

        it('retourne null pour E164 invalide', function () {
            $result = $this->cast->get($this->model, 'telephone', 'invalid', []);
            expect($result)->toBeNull();
        });

        it('retourne PhoneNumber pour E164 valide', function () {
            $result = $this->cast->get($this->model, 'telephone', '+33612345678', []);

            expect($result)->toBeInstanceOf(PhoneNumber::class);
            expect($result->telephoneE164)->toBe('+33612345678');
            expect($result->countryCode)->toBe('FR');
        });

        it('retourne PhoneNumber pour différents pays', function () {
            $belgiumPhone = $this->cast->get($this->model, 'telephone', '+32123456789', []);
            $germanPhone  = $this->cast->get($this->model, 'telephone', '+491234567890', []);

            expect($belgiumPhone)->toBeInstanceOf(PhoneNumber::class);
            expect($belgiumPhone->countryCode)->toBe('BE');

            expect($germanPhone)->toBeInstanceOf(PhoneNumber::class);
            expect($germanPhone->countryCode)->toBe('DE');
        });

        it('gère les formats E164 avec espaces', function () {
            $result = $this->cast->get($this->model, 'telephone', '+33 6 12 34 56 78', []);
            expect($result)->toBeNull(); // tryFromE164 attend format strict
        });
    });

    describe('set() - Persistence vers DB', function () {
        it('convertit PhoneNumber VO vers E164', function () {
            $phone  = PhoneNumber::fromE164('+33612345678');
            $result = $this->cast->set($this->model, 'telephone', $phone, []);

            expect($result)->toBe('+33612345678');
        });

        it('convertit string E164 vers E164', function () {
            $result = $this->cast->set($this->model, 'telephone', '+33612345678', []);
            expect($result)->toBe('+33612345678');
        });

        it('lève exception pour string invalide', function () {
            expect(fn () => $this->cast->set($this->model, 'telephone', 'invalid', []))
                ->toThrow(InvalidArgumentException::class);
        });

        it('lève exception pour format avec espaces (E164 strict requis)', function () {
            expect(fn () => $this->cast->set($this->model, 'telephone', '+33 6 12 34 56 78', []))
                ->toThrow(InvalidArgumentException::class);
        });

        it('gère différents pays correctement', function () {
            $belgiumResult = $this->cast->set($this->model, 'telephone', '+32123456789', []);
            $germanResult  = $this->cast->set($this->model, 'telephone', '+491234567890', []);

            expect($belgiumResult)->toBe('+32123456789');
            expect($germanResult)->toBe('+491234567890');
        });
    });

    describe('Cycle complet DB', function () {
        it('round-trip: set puis get conserve la valeur', function () {
            $originalPhone = PhoneNumber::fromE164('+33612345678');

            // Simule sauvegarde en DB
            $dbValue = $this->cast->set($this->model, 'telephone', $originalPhone, []);

            // Simule lecture depuis DB
            $retrievedPhone = $this->cast->get($this->model, 'telephone', $dbValue, []);

            expect($retrievedPhone)->toBeInstanceOf(PhoneNumber::class);
            expect($retrievedPhone->equals($originalPhone))->toBeTrue();
        });
    });
});
