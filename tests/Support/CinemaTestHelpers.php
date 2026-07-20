<?php

declare(strict_types=1);

use App\Domain\Shared\Enums\CodePays;
use App\Domain\Cinema\Entities\Cinema;
use App\Domain\Shared\ValueObjects\Email;
use App\Domain\Shared\ValueObjects\Address;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Shared\ValueObjects\PhoneNumber;

/**
 * Crée un cinéma de test basique avec des valeurs par défaut
 */
function createTestCinema(): Cinema
{
    return Cinema::creer('Cinéma Test', createTestAddress(), CodePays::France);
}

/**
 * Crée une adresse de test basique
 */
function createTestAddress(): Address
{
    return Address::fromArray([
        'rue'         => '123 Rue du Cinema',
        'ville'       => 'Paris',
        'code_postal' => '75001',
        'pays'        => 'FR',
    ]);
}

/**
 * Crée un cinéma de test avec nom et ville personnalisés
 */
function createTestCinemaWithVille(string $nom, string $ville): Cinema
{
    return Cinema::creer(
        nom: $nom,
        adresse: Address::fromArray([
            'rue'         => '123 Rue Test',
            'ville'       => $ville,
            'code_postal' => '75001',
            'pays'        => 'FR',
        ]),
        pays: CodePays::France,
        telephone: PhoneNumber::tryFromTelephoneEtPays('+33123456789', 'FR'),
        email: Email::tryFromString('test@example.com'),
        description: 'Description test'
    );
}

/**
 * Crée un cinéma de test inactif
 */
function createTestCinemaInactif(string $nom = 'Test Cinéma Inactif'): Cinema
{
    // Utiliser le constructeur pour créer un cinéma inactif
    return new Cinema(
        id: CinemaId::generate(),
        nom: $nom,
        adresse: Address::fromArray([
            'rue'         => '123 Rue Test',
            'ville'       => 'Paris',
            'code_postal' => '75001',
            'pays'        => 'FR',
        ]),
        pays: CodePays::France,
        telephone: PhoneNumber::tryFromTelephoneEtPays('+33123456789', 'FR'),
        email: Email::tryFromString('test@example.com'),
        estActif: false,
        description: 'Description test'
    );
}

/**
 * Crée un cinéma de test avec nom personnalisé et valeurs enrichies
 */
function createTestCinemaWithNom(string $nom): Cinema
{
    return Cinema::creer(
        nom: $nom,
        adresse: Address::fromArray([
            'rue'         => '123 Rue Test',
            'ville'       => 'Paris',
            'code_postal' => '75001',
            'pays'        => 'FR',
        ]),
        pays: CodePays::France,
        telephone: PhoneNumber::tryFromTelephoneEtPays('+33123456789', 'FR'),
        email: Email::tryFromString('test@example.com'),
        description: 'Description test'
    );
}
