<?php

declare(strict_types=1);

namespace App\Application\Cinema\DTOs;

final readonly class SalleListItemDto
{
    /**
     * @param string[] $technologies
     * @param array<string, mixed>|null $configurationSieges
     */
    public function __construct(
        public string $uuid,
        public string $nom,
        public int $numero,
        public int $capaciteTotale,
        public array $technologies,
        public bool $accessibilitePmr,
        public bool $climatisation,
        public string $qualiteSon,
        public string $tailleEcran,
        public string $typeEcran,
        public ?array $configurationSieges,
        public ?float $tarifSupplement,
        public string $statut,
        public string $cinemaUuid,
        public string $cinemaNom,
    ) {}

    public function getFormattedCapacity(): string
    {
        return number_format($this->capaciteTotale, 0, ',', ' ') . ' places';
    }

    public function getTechnologiesString(): string
    {
        return implode(', ', array_map('strtoupper', $this->technologies));
    }

    public function isPremium(): bool
    {
        return in_array('4dx', $this->technologies) ||
               in_array('imax', $this->technologies) ||
               in_array('dolby_atmos', $this->technologies);
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
            'uuid'                 => $this->uuid,
            'nom'                  => $this->nom,
            'numero'               => $this->numero,
            'capacite_totale'      => $this->capaciteTotale,
            'technologies'         => $this->technologies,
            'accessibilite_pmr'    => $this->accessibilitePmr,
            'climatisation'        => $this->climatisation,
            'qualite_son'          => $this->qualiteSon,
            'taille_ecran'         => $this->tailleEcran,
            'type_ecran'           => $this->typeEcran,
            'configuration_sieges' => $this->configurationSieges,
            'tarif_supplement'     => $this->tarifSupplement,
            'statut'               => $this->statut,
            'cinema_uuid'          => $this->cinemaUuid,
            'cinema_nom'           => $this->cinemaNom,
            'formatted_capacity'   => $this->getFormattedCapacity(),
            'technologies_string'  => $this->getTechnologiesString(),
            'is_premium'           => $this->isPremium(),
        ];
    }
}
