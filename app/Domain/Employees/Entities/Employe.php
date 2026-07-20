<?php

declare(strict_types=1);

namespace App\Domain\Employees\Entities;

use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Employees\ValueObjects\EmploiId;

/**
 * Entity représentant un employé pour les besoins des commandes
 * C'est un wrapper autour des données d'emploi
 */
final class Employe
{
    public function __construct(
        public readonly EmploiId $id,
        public readonly CinemaId $cinemaId,
        public readonly string $titrePoste,
        public readonly string $statut,
        public readonly bool $estActif,
    ) {}

    /**
     * Vérifie si l'employé travaille dans le cinéma donné
     */
    public function travailleDansCinema(CinemaId $cinemaId): bool
    {
        return $this->cinemaId->equals($cinemaId) && $this->estActif;
    }

    /**
     * Vérifie si l'employé est actif
     */
    public function isActive(): bool
    {
        return $this->estActif && $this->statut === 'actif';
    }
}
