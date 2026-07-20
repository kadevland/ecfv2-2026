<?php

declare(strict_types=1);

namespace App\Application\Cinema\DTOs;

use DateTimeImmutable;
use InvalidArgumentException;
use App\Domain\Shared\ValueObjects\HorairesOuverture;

final readonly class CinemaDetailDto
{
    /**
     * @param array<string, mixed> $salles
     * @param array<string, mixed> $seancesAVenir
     * @param string[] $services
     * @param array<string, mixed> $acces
     * @param array<string, mixed> $horairesArray
     */
    public function __construct(
        public string $uuid,
        public string $nom,
        public string $pays,
        public string $adresse,
        public string $ville,
        public string $codePostal,
        public ?string $telephone = null,
        public ?string $email = null,
        public ?string $siteWeb = null,
        public ?string $description = null,
        public bool $estActif = true,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public int $nombreSalles = 0,
        public ?HorairesOuverture $horairesOuverture = null,
        public bool $accessibilitePmr = false,
        public array $salles = [],
        public array $seancesAVenir = [],
        public array $services = [],
        public array $acces = [],
        public array $horairesArray = [], // Temporaire pour les horaires en array
        public ?DateTimeImmutable $createdAt = null,
        public ?DateTimeImmutable $updatedAt = null,
    ) {}

    /**
     * Getter pour backward compatibility (accès depuis Blade)
     */
    public function __get(string $name): mixed
    {
        return match ($name) {
            'horaires'           => $this->horairesOuverture,
            'horaires_ouverture' => $this->horairesArray, // Support pour Blade
            'code_postal'        => $this->codePostal, // Support snake_case
            'nombre_salles'      => $this->nombreSalles, // Support snake_case
            default              => throw new InvalidArgumentException("Property {$name} does not exist on CinemaDetailDto")
        };
    }

    /**
     * @return array<string, mixed>
     */
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'uuid'               => $this->uuid,
            'nom'                => $this->nom,
            'pays'               => $this->pays,
            'adresse'            => $this->adresse,
            'ville'              => $this->ville,
            'code_postal'        => $this->codePostal,
            'telephone'          => $this->telephone,
            'email'              => $this->email,
            'site_web'           => $this->siteWeb,
            'description'        => $this->description,
            'est_actif'          => $this->estActif,
            'latitude'           => $this->latitude,
            'longitude'          => $this->longitude,
            'nombre_salles'      => $this->nombreSalles,
            'horaires_ouverture' => $this->horairesOuverture?->toArray(),
            'horaires'           => $this->horairesOuverture, // Alias pour compatibility
            'accessibilite_pmr'  => $this->accessibilitePmr,
            'salles'             => $this->salles,
            'seances_a_venir'    => $this->seancesAVenir,
            'services'           => $this->services,
            'acces'              => $this->acces,
            'created_at'         => $this->createdAt?->format('Y-m-d H:i:s'),
            'updated_at'         => $this->updatedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
