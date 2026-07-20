<?php

declare(strict_types=1);

namespace App\Application\Cinema\Commands;

use App\Application\Contracts\CommandInterface;

final readonly class CreateSalleCommand implements CommandInterface
{
    /**
     * @param string[] $qualitesVideo
     * @param string[] $qualitesAudio
     */
    public function __construct(
        public string $cinemaId,
        public string $nom,
        public int $capaciteTotale,
        public int $nombreRangees,
        public int $placesParRangee,
        public int $placesStandard = 0,
        public int $placesPremium = 0,
        public int $placesPmr = 0,
        public array $qualitesVideo = [],
        public array $qualitesAudio = [],
        public bool $climatisation = true,
        public bool $accessibilitePmr = true,
        public string $statut = 'ACTIVE',
        public bool $estActive = true,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            cinemaId: $data['cinema_id'],
            nom: $data['nom'],
            capaciteTotale: (int) $data['capacite_totale'],
            nombreRangees: (int) $data['nombre_rangees'],
            placesParRangee: (int) $data['places_par_rangee'],
            placesStandard: (int) ($data['places_standard'] ?? 0),
            placesPremium: (int) ($data['places_premium'] ?? 0),
            placesPmr: (int) ($data['places_pmr'] ?? 0),
            qualitesVideo: $data['qualites_video'] ?? [],
            qualitesAudio: $data['qualites_audio'] ?? [],
            climatisation: (bool) ($data['climatisation'] ?? true),
            accessibilitePmr: (bool) ($data['accessibilite_pmr'] ?? true),
            statut: $data['statut'] ?? 'ACTIVE',
            estActive: (bool) ($data['est_active'] ?? true),
        );
    }
}
