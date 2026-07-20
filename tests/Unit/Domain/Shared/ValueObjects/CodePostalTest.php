<?php

declare(strict_types=1);

use App\Domain\Shared\ValueObjects\CodePostal;

describe('CodePostal ValueObject', function () {

    it('accepte un code postal valide', function () {
        $codePostal = CodePostal::fromString('75001');

        expect($codePostal->value)->toBe('75001');
        expect((string) $codePostal)->toBe('75001');
    });

    it('accepte un code postal belge', function () {
        $codePostal = CodePostal::fromString('1000');

        expect($codePostal->value)->toBe('1000');
    });

    it('rejette un code postal vide', function () {
        expect(fn () => CodePostal::fromString(''))
            ->toThrow(InvalidArgumentException::class);
    });

    it('rejette un code postal invalide', function () {
        expect(fn () => CodePostal::fromString('ABC'))
            ->toThrow(InvalidArgumentException::class);
    });

    it('compare deux codes postaux', function () {
        $cp1 = CodePostal::fromString('75001');
        $cp2 = CodePostal::fromString('75001');

        expect($cp1->equals($cp2))->toBeTrue();
    });
});
