<?php

declare(strict_types=1);

use App\Domain\Shared\ValueObjects\Email;

describe('Email Value Object', function () {
    it('peut créer un email valide', function () {
        $email = Email::fromString('test@example.com');

        expect($email->toString())->toBe('test@example.com');
        expect($email->getLocalPart())->toBe('test');
        expect($email->getDomain())->toBe('example.com');
    });

    it('normalise les emails en minuscules', function () {
        $email = Email::fromString('TEST@EXAMPLE.COM');

        expect($email->toString())->toBe('test@example.com');
        expect($email->getLocalPart())->toBe('test');
        expect($email->getDomain())->toBe('example.com');
    });

    it('lève exception pour email invalide', function () {
        expect(fn () => Email::fromString('invalid'))
            ->toThrow(InvalidArgumentException::class);
    });

    it('tryFromString retourne null pour données invalides', function () {
        expect(Email::tryFromString('invalid'))->toBeNull();
        expect(Email::tryFromString(null))->toBeNull();
    });

    it('compare deux emails par égalité', function () {
        $email1 = Email::fromString('test@example.com');
        $email2 = Email::fromString('TEST@EXAMPLE.COM');

        expect($email1->equals($email2))->toBeTrue();
    });
});
