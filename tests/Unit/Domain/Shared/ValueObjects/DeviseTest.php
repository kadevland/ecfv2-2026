<?php

declare(strict_types=1);

use InvalidArgumentException;
use App\Domain\Shared\ValueObjects\Devise;

describe('Devise ValueObject', function () {

    it('accepte EUR', function () {
        $devise = Devise::fromString('EUR');

        expect($devise->value)->toBe('EUR');
        expect((string) $devise)->toBe('EUR');
    });

    it('accepte USD', function () {
        $devise = Devise::fromString('USD');

        expect($devise->value)->toBe('USD');
    });

    it('rejette une devise vide', function () {
        expect(fn () => Devise::fromString(''))
            ->toThrow(InvalidArgumentException::class);
    });

    it('rejette une devise invalide', function () {
        expect(fn () => Devise::fromString('XYZ'))
            ->toThrow(InvalidArgumentException::class);
    });

    it('compare deux devises', function () {
        $devise1 = Devise::fromString('EUR');
        $devise2 = Devise::fromString('EUR');

        expect($devise1->equals($devise2))->toBeTrue();
    });
});
