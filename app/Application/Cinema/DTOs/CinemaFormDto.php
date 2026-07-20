<?php

declare(strict_types=1);

namespace App\Application\Cinema\DTOs;

use App\Domain\Shared\ValueObjects\HorairesOuverture;

final readonly class CinemaFormDto
{
    public function __construct(
        public ?string $uuid = null,
        public string $nom = '',
        public string $pays = 'FR',
        public string $rue = '',
        public string $ville = '',
        public string $codePostal = '',
        public string $telephone = '',
        public string $email = '',
        public string $siteWeb = '',
        public string $description = '',
        public bool $estActif = true,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?HorairesOuverture $horaires = null,
    ) {}

    /**
     * Créer un DTO depuis un CinemaDetailDto pour l'édition
     */
    public static function fromDetailDto(CinemaDetailDto $detail): self
    {
        return new self(
            uuid: $detail->uuid,
            nom: $detail->nom,
            pays: $detail->pays,
            rue: $detail->adresse,
            ville: $detail->ville,
            codePostal: $detail->codePostal,
            telephone: $detail->telephone ?? '',
            email: $detail->email ?? '',
            siteWeb: $detail->siteWeb ?? '',
            description: $detail->description ?? '',
            estActif: $detail->estActif,
            latitude: $detail->latitude,
            longitude: $detail->longitude,
            horaires: $detail->horairesOuverture,
        );
    }

    /**
     * Créer un DTO vide pour la création
     */
    public static function empty(): self
    {
        return new self;
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
            'uuid'        => $this->uuid,
            'nom'         => $this->nom,
            'pays'        => $this->pays,
            'rue'         => $this->rue,
            'ville'       => $this->ville,
            'code_postal' => $this->codePostal,
            'telephone'   => $this->telephone,
            'email'       => $this->email,
            'site_web'    => $this->siteWeb,
            'description' => $this->description,
            'est_actif'   => $this->estActif,
            'latitude'    => $this->latitude,
            'longitude'   => $this->longitude,
            'horaires'    => $this->horaires,
        ];
    }
}
