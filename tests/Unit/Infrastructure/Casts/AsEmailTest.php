<?php

declare(strict_types=1);

use App\Infrastructure\Casts\AsEmail;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\ValueObjects\Email;

describe('AsEmail Cast', function () {
    beforeEach(function () {
        $this->cast  = new AsEmail;
        $this->model = new class extends Model {};
    });

    describe('get() - Hydration depuis DB', function () {
        it('retourne null pour valeur null', function () {
            $result = $this->cast->get($this->model, 'email', null, []);
            expect($result)->toBeNull();
        });

        it('retourne null pour email invalide', function () {
            $result = $this->cast->get($this->model, 'email', 'invalid-email', []);
            expect($result)->toBeNull();
        });

        it('retourne Email pour email valide', function () {
            $result = $this->cast->get($this->model, 'email', 'test@example.com', []);

            expect($result)->toBeInstanceOf(Email::class);
            expect($result->toString())->toBe('test@example.com');
            expect($result->getLocalPart())->toBe('test');
            expect($result->getDomain())->toBe('example.com');
        });

        it('normalise les emails en minuscules', function () {
            $result = $this->cast->get($this->model, 'email', 'TEST@EXAMPLE.COM', []);

            expect($result)->toBeInstanceOf(Email::class);
            expect($result->toString())->toBe('test@example.com');
        });

        it('gère différents formats d\'email', function () {
            $simpleEmail  = $this->cast->get($this->model, 'email', 'user@domain.org', []);
            $complexEmail = $this->cast->get($this->model, 'email', 'user.name+tag@subdomain.example.com', []);

            expect($simpleEmail)->toBeInstanceOf(Email::class);
            expect($simpleEmail->getDomain())->toBe('domain.org');

            expect($complexEmail)->toBeInstanceOf(Email::class);
            expect($complexEmail->getLocalPart())->toBe('user.name+tag');
            expect($complexEmail->getDomain())->toBe('subdomain.example.com');
        });
    });

    describe('set() - Persistence vers DB', function () {
        it('convertit Email VO vers string', function () {
            $email  = Email::fromString('test@example.com');
            $result = $this->cast->set($this->model, 'email', $email, []);

            expect($result)->toBe('test@example.com');
        });

        it('convertit string email vers string normalisé', function () {
            $result = $this->cast->set($this->model, 'email', 'TEST@EXAMPLE.COM', []);
            expect($result)->toBe('test@example.com');
        });

        it('lève exception pour string invalide', function () {
            expect(fn () => $this->cast->set($this->model, 'email', 'invalid-email', []))
                ->toThrow(InvalidArgumentException::class);
        });

        it('gère les emails avec caractères spéciaux', function () {
            $result = $this->cast->set($this->model, 'email', 'user.name+tag@example.com', []);
            expect($result)->toBe('user.name+tag@example.com');
        });
    });

    describe('Cycle complet DB', function () {
        it('round-trip: set puis get conserve la valeur', function () {
            $originalEmail = Email::fromString('user@example.com');

            // Simule sauvegarde en DB
            $dbValue = $this->cast->set($this->model, 'email', $originalEmail, []);

            // Simule lecture depuis DB
            $retrievedEmail = $this->cast->get($this->model, 'email', $dbValue, []);

            expect($retrievedEmail)->toBeInstanceOf(Email::class);
            expect($retrievedEmail->equals($originalEmail))->toBeTrue();
        });

        it('round-trip avec normalisation', function () {
            // String avec casse mixte
            $dbValue        = $this->cast->set($this->model, 'email', 'User@EXAMPLE.COM', []);
            $retrievedEmail = $this->cast->get($this->model, 'email', $dbValue, []);

            expect($retrievedEmail)->toBeInstanceOf(Email::class);
            expect($retrievedEmail->toString())->toBe('user@example.com');
        });
    });
});
