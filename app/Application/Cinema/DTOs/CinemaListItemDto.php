<?php

declare(strict_types=1);

namespace App\Application\Cinema\DTOs;

final readonly class CinemaListItemDto
{
    /**
     * @param array<string, mixed> $horairesOuverture
     */
    public function __construct(
        public string $uuid,
        public string $nom,
        public string $adresse,
        public string $ville,
        public string $codePostal,
        public ?string $telephone = null,
        public ?string $email = null,
        public int $nombreSalles = 0,
        public array $horairesOuverture = [],
        public bool $accessibilitePmr = false,
        public ?float $latitude = null,
        public ?float $longitude = null,
    ) {}

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
            'adresse'            => $this->adresse,
            'ville'              => $this->ville,
            'code_postal'        => $this->codePostal,
            'telephone'          => $this->telephone,
            'email'              => $this->email,
            'nombre_salles'      => $this->nombreSalles,
            'horaires_ouverture' => $this->horairesOuverture,
            'accessibilite_pmr'  => $this->accessibilitePmr,
            'latitude'           => $this->latitude,
            'longitude'          => $this->longitude,
        ];
    }
}
