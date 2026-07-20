<?php

declare(strict_types=1);

use InvalidArgumentException;
use App\Domain\Shared\ValueObjects\Url;

describe('Url ValueObject', function () {

    it('accepte une URL valide', function () {
        $url = Url::fromString('https://cinema.fr');

        expect($url->value)->toBe('https://cinema.fr');
        expect((string) $url)->toBe('https://cinema.fr');
    });

    it('accepte une URL avec www', function () {
        $url = Url::fromString('https://www.cinema.fr');

        expect($url->value)->toBe('https://www.cinema.fr');
    });

    it('rejette une URL vide', function () {
        expect(fn () => Url::fromString(''))
            ->toThrow(InvalidArgumentException::class);
    });

    it('rejette une URL invalide', function () {
        expect(fn () => Url::fromString('invalid-url'))
            ->toThrow(InvalidArgumentException::class);
    });

    it('compare deux URLs', function () {
        $url1 = Url::fromString('https://cinema.fr');
        $url2 = Url::fromString('https://cinema.fr');

        expect($url1->equals($url2))->toBeTrue();
    });
});
