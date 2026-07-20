<?php

declare(strict_types=1);

use App\Domain\Shared\ValueObjects\Nom;

describe('Nom ValueObject', function () {

    it('accepte un nom valide', function () {
        $nom = Nom::fromString('Dupont');

        expect($nom->value)->toBe('Dupont');
        expect((string) $nom)->toBe('Dupont');
    });

    it('accepte les minuscules', function () {
        $nom = Nom::fromString('martin');

        expect($nom->value)->toBe('martin');
    });

    it('gère les caractères spéciaux', function () {
        $nom = Nom::fromString('O\'Connor');

        expect($nom->value)->toBe('O\'Connor');
    });

    it('rejette un nom vide', function () {
        expect(fn () => Nom::fromString(''))
            ->toThrow(InvalidArgumentException::class);
    });

    it('accepte un nom court', function () {
        $nom = Nom::fromString('A');

        expect($nom->value)->toBe('A');
    });

    it('compare deux noms', function () {
        $nom1 = Nom::fromString('Dupont');
        $nom2 = Nom::fromString('DUPONT');

        expect($nom1->equals($nom2))->toBeTrue();
    });
});
