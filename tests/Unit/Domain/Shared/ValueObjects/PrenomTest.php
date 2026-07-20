<?php

declare(strict_types=1);

use InvalidArgumentException;
use App\Domain\Shared\ValueObjects\Prenom;

describe('Prenom ValueObject', function () {

    it('accepte un prénom valide', function () {
        $prenom = Prenom::fromString('Jean');

        expect($prenom->value)->toBe('Jean');
        expect((string) $prenom)->toBe('Jean');
    });

    it('capitalise la première lettre', function () {
        $prenom = Prenom::fromString('marie');

        expect($prenom->value)->toBe('Marie');
    });

    it('gère les prénoms composés', function () {
        $prenom = Prenom::fromString('jean-claude');

        expect($prenom->value)->toBe('Jean-Claude');
    });

    it('rejette un prénom vide', function () {
        expect(fn () => Prenom::fromString(''))
            ->toThrow(InvalidArgumentException::class);
    });

    it('rejette un prénom trop court', function () {
        expect(fn () => Prenom::fromString('A'))
            ->toThrow(InvalidArgumentException::class);
    });

    it('compare deux prénoms', function () {
        $prenom1 = Prenom::fromString('Pierre');
        $prenom2 = Prenom::fromString('PIERRE');

        expect($prenom1->equals($prenom2))->toBeTrue();
    });
});
