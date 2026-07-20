<?php

declare(strict_types=1);

use App\Infrastructure\Casts\EmailCast;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\ValueObjects\Email;

describe('EmailCast (alternative)', function () {
    beforeEach(function () {
        $this->cast  = new EmailCast;
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
        });

        it('normalise automatiquement', function () {
            $result = $this->cast->get($this->model, 'email', 'TEST@EXAMPLE.COM', []);

            expect($result)->toBeInstanceOf(Email::class);
            expect($result->toString())->toBe('test@example.com');
        });
    });

    describe('set() - Persistence vers DB', function () {
        it('convertit Email VO vers string via value property', function () {
            $email  = Email::fromString('test@example.com');
            $result = $this->cast->set($this->model, 'email', $email, []);

            // NOTE: Utilise ->value pas ->toString()
            expect($result)->toBe('test@example.com');
        });

        it('convertit string email vers string via VO', function () {
            $result = $this->cast->set($this->model, 'email', 'USER@EXAMPLE.COM', []);

            // Passe par Email::fromString()->value
            expect($result)->toBe('user@example.com');
        });

        it('retourne valeur originale pour autres types', function () {
            $result = $this->cast->set($this->model, 'email', null, []);
            expect($result)->toBeNull();
        });

        it('lève exception pour string invalide', function () {
            expect(fn () => $this->cast->set($this->model, 'email', 'invalid-email', []))
                ->toThrow(InvalidArgumentException::class);
        });
    });

    describe('Différence avec AsEmail', function () {
        it('utilise ->value au lieu de ->toString()', function () {
            $email = Email::fromString('test@example.com');

            // EmailCast utilise ->value
            $result = $this->cast->set($this->model, 'email', $email, []);
            expect($result)->toBe($email->value);

            // Doit être identique à ->toString() pour Email
            expect($result)->toBe($email->toString());
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
    });
});
