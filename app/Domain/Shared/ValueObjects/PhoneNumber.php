<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use Exception;
use InvalidArgumentException;
use Respect\Validation\Validator as v;
use Propaganistas\LaravelPhone\PhoneNumber as LaravelPhone;

/**
 * Value Object pour numéros de téléphone
 * Utilise libphonenumber via propaganistas/laravel-phone
 * Pattern PHP 8.4 avec Property Hooks et encapsulation LaravelPhone
 * Stockage JSONB : {"e164": "+33612345678", "search": "33612345678"}
 */
final class PhoneNumber
{
    private const MAX_LENGTH = 20; // E.164 max length

    public string $telephoneInternational {
        get => $this->laravelPhone->formatInternational(); // "+33 6 12 34 56 78"
    }

    public string $telephoneNational {
        get => $this->laravelPhone->formatNational(); // "06 12 34 56 78"
    }

    public string $telephoneE164 {
        get => $this->laravelPhone->formatE164(); // "+33612345678"
    }

    public string $countryCode {
        get => $this->laravelPhone->getCountry() ?? ''; // "FR", "BE", etc.
    }

    public int $indicatifTelephonique {
        get => $this->laravelPhone->toLibPhoneObject()->getCountryCode(); // Code pays numérique
    }

    public bool $isMobile {
        get {
            try {
                $type = $this->laravelPhone->getType();

                return $type === \libphonenumber\PhoneNumberType::MOBILE;
            } catch (Exception) {
                return false; // Fallback safe
            }
        }
    }

    private function __construct(
        private readonly LaravelPhone $laravelPhone
    ) {
        // Constructor simple : LaravelPhone déjà validé par creeLaravelPhone()
    }

    /**
     * Représentation string par défaut (E.164)
     */
    public function __toString(): string
    {
        return $this->telephoneE164;
    }

    /**
     * Factory : téléphone brut + code pays
     *
     * @param string $rawNumber Numéro brut (06 12 34 56 78, 0123456789, etc.)
     * @param string $countryCode Code pays ISO (FR, BE, CH, etc.)
     */
    public static function fromTelephoneEtPays(string $rawNumber, string $countryCode): self
    {
        $laravelPhone = self::creeLaravelPhone($rawNumber, $countryCode);

        return new self($laravelPhone);
    }

    /**
     * Factory : format international E.164
     *
     * @param string $e164 Numéro international (+33612345678, +33 6 12 34 56 78, etc.)
     */
    public static function fromInternationalFormat(string $e164): self
    {
        $laravelPhone = self::creeLaravelPhone(trim($e164), null);

        return new self($laravelPhone);
    }

    /**
     * Factory : format E.164 strict
     *
     * @param string $e164 Numéro E.164 strict (+33612345678)
     */
    public static function fromE164(string $e164): self
    {
        $laravelPhone = self::creeLaravelPhone(trim($e164), null);

        return new self($laravelPhone);
    }

    /**
     * Factory safe : téléphone brut + code pays (retourne null si erreur)
     */
    public static function tryFromTelephoneEtPays(?string $rawNumber, ?string $countryCode): ?self
    {
        if ($rawNumber === null || $countryCode === null) {
            return null;
        }

        try {
            return self::fromTelephoneEtPays($rawNumber, $countryCode);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * Factory safe : format international (retourne null si erreur)
     */
    public static function tryFromInternationalFormat(?string $e164): ?self
    {
        if ($e164 === null) {
            return null;
        }

        try {
            return self::fromInternationalFormat($e164);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * Factory safe : format E.164 strict (retourne null si erreur)
     */
    public static function tryFromE164(?string $e164): ?self
    {
        if ($e164 === null) {
            return null;
        }

        try {
            return self::fromE164($e164);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    // Méthodes compatibilité (délègent aux Property Hooks)
    public function toE164(): string
    {
        return $this->telephoneE164;
    }

    public function toInternational(): string
    {
        return $this->telephoneInternational;
    }

    public function toNational(): string
    {
        return $this->telephoneNational;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getCountryCallingCode(): int
    {
        return $this->indicatifTelephonique;
    }

    public function getType(): string
    {
        return $this->isMobile ? 'mobile' : 'fixed_line';
    }

    public function isMobile(): bool
    {
        return $this->isMobile;
    }

    /**
     * Égalité basée sur E.164
     */
    public function equals(self $other): bool
    {
        return $this->telephoneE164 === $other->telephoneE164;
    }

    /**
     * Helper statique : validation + création LaravelPhone sécurisée
     */
    private static function creeLaravelPhone(string $value, ?string $country): LaravelPhone
    {
        // Validation AVANT création LaravelPhone
        self::enforceInvariant($value, $country);

        try {
            $laravelPhone = new LaravelPhone($value, $country);

            // Validation supplémentaire pour format avec pays
            if ($country !== null && !$laravelPhone->isOfCountry($country)) {
                throw new InvalidArgumentException(
                    "Le numéro '$value' n'appartient pas au pays '$country'"
                );
            }

            return $laravelPhone;
        } catch (Exception $e) {
            throw new InvalidArgumentException(
                "Impossible de créer le numéro de téléphone: {$e->getMessage()}",
                previous: $e
            );
        }
    }

    /**
     *  Validation centralisée statique (avant construction LaravelPhone)
     */
    private static function enforceInvariant(string $value, ?string $country): void
    {
        self::validateNotEmpty($value);
        self::validateLength($value);

        if ($country !== null) {
            self::validateCountryCode($country);
        } else {
            // Si pas de pays, on assume format international
            self::validateInternationalFormat($value);
        }
    }

    private static function validateNotEmpty(string $value): void
    {
        if (!v::notEmpty()->validate($value)) {
            throw new InvalidArgumentException('Le numéro de téléphone ne peut pas être vide');
        }
    }

    private static function validateLength(string $value): void
    {
        if (!v::length(4, self::MAX_LENGTH)->validate($value)) {
            throw new InvalidArgumentException(
                'Le numéro doit contenir entre 4 et ' . self::MAX_LENGTH . ' caractères'
            );
        }
    }

    private static function validateCountryCode(string $country): void
    {
        if (!v::length(2, 2)->alpha()->uppercase()->validate($country)) {
            throw new InvalidArgumentException(
                "Code pays invalide: '$country' (format attendu: FR, BE, CH, etc.)"
            );
        }
    }

    private static function validateInternationalFormat(string $value): void
    {
        if (!v::startsWith('+')->validate($value)) {
            throw new InvalidArgumentException(
                "Format international requis (doit commencer par '+'): $value"
            );
        }

        // Validation basique E.164 : +[1-3 digits][up to 15 digits]
        if (!v::regex('/^\+[1-9]\d{1,14}$/')->validate($value)) {
            throw new InvalidArgumentException(
                "Le numéro '$value' n'est pas au format E.164 valide"
            );
        }
    }
}
