<?php

declare(strict_types=1);

use App\Domain\Shared\ValueObjects\Address;

describe('Address ValueObject', function () {

    it('peut créer une adresse complète', function () {
        $address = Address::create(
            rue: '123 Rue de la Paix',
            ville: 'Paris',
            codePostal: '75001'
        );

        expect($address->rue)->toBe('123 Rue de la Paix');
        expect($address->ville)->toBe('Paris');
        expect($address->codePostal)->toBe('75001');
    });

    it('peut créer une adresse avec pays', function () {
        $address = Address::create(
            rue: '456 Avenue Louise',
            ville: 'Bruxelles',
            codePostal: '1000',
            pays: 'Belgique'
        );

        expect($address->pays)->toBe('Belgique');
    });

    it('formate l\'adresse en string', function () {
        $address = Address::create(
            rue: '789 Boulevard Saint-Michel',
            ville: 'Lyon',
            codePostal: '69001'
        );

        $formatted = (string) $address;
        expect($formatted)->toContain('789 Boulevard Saint-Michel');
        expect($formatted)->toContain('Lyon');
        expect($formatted)->toContain('69001');
    });

    it('compare deux adresses', function () {
        $address1 = Address::create(
            rue: '123 Rue Test',
            ville: 'Paris',
            codePostal: '75001'
        );

        $address2 = Address::create(
            rue: '123 Rue Test',
            ville: 'Paris',
            codePostal: '75001'
        );

        expect($address1->equals($address2))->toBeTrue();
    });
});
