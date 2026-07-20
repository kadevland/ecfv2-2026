<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;
use Respect\Validation\Validator as v;

final readonly class CoordonneesGps
{
    // Mapping keys
    public const KEY_LATITUDE = 'latitude';

    public const KEY_LONGITUDE = 'longitude';

    // Champs requis pour validation
    private const REQUIRED_FIELDS = [
        self::KEY_LATITUDE,
        self::KEY_LONGITUDE,
    ];

    // Limites géographiques mondiales
    private const MIN_LATITUDE = -90.0;

    private const MAX_LATITUDE = 90.0;

    private const MIN_LONGITUDE = -180.0;

    private const MAX_LONGITUDE = 180.0;

    // Précision autorisée (nombre de décimales)
    private const MAX_PRECISION = 8;

    public function __construct(
        public readonly float $latitude,
        public readonly float $longitude,
    ) {
        $this->enforceInvariant();
    }

    /**
     * @param array{latitude: float|string, longitude: float|string} $data
     */
    public static function fromArray(array $data): self
    {
        // Validation des champs requis
        foreach (self::REQUIRED_FIELDS as $field) {
            if (!array_key_exists($field, $data)) {
                throw new InvalidArgumentException("Le champ '$field' est requis");
            }
        }

        return new self(
            latitude: (float) $data[self::KEY_LATITUDE],
            longitude: (float) $data[self::KEY_LONGITUDE],
        );
    }

    /**
     * @param array<string, mixed>|null $data
     */
    public static function tryFromArray(?array $data): ?self
    {
        if ($data === null || empty($data)) {
            return null;
        }

        try {
            return self::fromArray($data);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    public static function creer(float $latitude, float $longitude): self
    {
        return new self($latitude, $longitude);
    }

    /**
     * Créer depuis des chaînes de caractères (formulaires)
     */
    public static function fromStrings(string $latitude, string $longitude): self
    {
        $lat = filter_var($latitude, FILTER_VALIDATE_FLOAT);
        $lng = filter_var($longitude, FILTER_VALIDATE_FLOAT);

        if ($lat === false) {
            throw new InvalidArgumentException("La latitude '$latitude' n'est pas un nombre valide");
        }

        if ($lng === false) {
            throw new InvalidArgumentException("La longitude '$longitude' n'est pas un nombre valide");
        }

        return new self($lat, $lng);
    }

    /**
     * Retourne la liste des champs requis pour validation
     *
     * @return array<string>
     */
    public static function getRequiredFields(): array
    {
        return self::REQUIRED_FIELDS;
    }

    /**
     * Calcule la distance en kilomètres vers un autre point
     */
    public function distanceVers(CoordonneesGps $autre): float
    {
        $rayonTerre = 6371; // Rayon de la Terre en kilomètres

        $latRad1 = deg2rad($this->latitude);
        $lonRad1 = deg2rad($this->longitude);
        $latRad2 = deg2rad($autre->latitude);
        $lonRad2 = deg2rad($autre->longitude);

        $deltaLat = $latRad2 - $latRad1;
        $deltaLon = $lonRad2 - $lonRad1;

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
            cos($latRad1) * cos($latRad2) *
            sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $rayonTerre * $c;
    }

    /**
     * Formate les coordonnées pour affichage
     */
    public function toString(int $precision = 6): string
    {
        return sprintf("%.{$precision}f, %.{$precision}f", $this->latitude, $this->longitude);
    }

    /**
     * Génère une URL Google Maps
     */
    public function getGoogleMapsUrl(): string
    {
        return sprintf(
            'https://www.google.com/maps?q=%f,%f',
            $this->latitude,
            $this->longitude
        );
    }

    /**
     * Vérifie si le point est en France métropolitaine (approximatif)
     */
    public function isInFranceMetropolitaine(): bool
    {
        return $this->latitude >= 41.0 && $this->latitude <= 51.5 &&
               $this->longitude >= -5.5 && $this->longitude <= 10.0;
    }

    /**
     * Vérifie si le point est en Belgique (approximatif)
     */
    public function isInBelgique(): bool
    {
        return $this->latitude >= 49.4 && $this->latitude <= 51.6 &&
               $this->longitude >= 2.5 && $this->longitude <= 6.5;
    }

    public function equals(CoordonneesGps $other): bool
    {
        // Comparaison avec précision pour éviter les problèmes de float
        return abs($this->latitude - $other->latitude) < 0.0000001 &&
               abs($this->longitude - $other->longitude) < 0.0000001;
    }

    /**
     * @return array{latitude: float, longitude: float}
     */
    public function toArray(): array
    {
        return [
            self::KEY_LATITUDE  => $this->latitude,
            self::KEY_LONGITUDE => $this->longitude,
        ];
    }

    private function enforceInvariant(): void
    {
        $this->validateLatitude();
        $this->validateLongitude();
        $this->validatePrecision();
    }

    private function validateLatitude(): void
    {
        if (!v::floatVal()->between(self::MIN_LATITUDE, self::MAX_LATITUDE)
            ->validate($this->latitude)) {
            throw new InvalidArgumentException(
                sprintf(
                    'La latitude doit être comprise entre %s et %s (reçu: %s)',
                    self::MIN_LATITUDE,
                    self::MAX_LATITUDE,
                    $this->latitude
                )
            );
        }
    }

    private function validateLongitude(): void
    {
        if (!v::floatVal()->between(self::MIN_LONGITUDE, self::MAX_LONGITUDE)
            ->validate($this->longitude)) {
            throw new InvalidArgumentException(
                sprintf(
                    'La longitude doit être comprise entre %s et %s (reçu: %s)',
                    self::MIN_LONGITUDE,
                    self::MAX_LONGITUDE,
                    $this->longitude
                )
            );
        }
    }

    private function validatePrecision(): void
    {
        // Vérifie que la précision n'est pas excessive
        $latString = (string) $this->latitude;
        $lngString = (string) $this->longitude;

        $latDecimalPlaces = ($latPos = strrchr($latString, '.')) !== false ? strlen(substr($latPos, 1)) : 0;
        $lngDecimalPlaces = ($lngPos = strrchr($lngString, '.')) !== false ? strlen(substr($lngPos, 1)) : 0;

        if ($latDecimalPlaces > self::MAX_PRECISION || $lngDecimalPlaces > self::MAX_PRECISION) {
            throw new InvalidArgumentException(
                sprintf(
                    'La précision ne peut pas dépasser %d décimales',
                    self::MAX_PRECISION
                )
            );
        }
    }
}
