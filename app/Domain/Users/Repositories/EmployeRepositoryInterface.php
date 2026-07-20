<?php

declare(strict_types=1);

namespace App\Domain\Users\Repositories;

use App\Domain\Employees\Entities\Employe;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Employees\ValueObjects\EmploiId;

/**
 * Interface pour l'accès aux employés
 * Note: Les employés sont représentés par les entités Emploi qui lient User + Cinema
 */
interface EmployeRepositoryInterface
{
    /**
     * Trouve un emploi (employé) par son identifiant
     */
    public function findById(EmploiId $id): ?Employe;

    /**
     * Trouve l'emploi actif d'un utilisateur dans un cinéma donné
     */
    public function findActiveByUserAndCinema(string $userUuid, CinemaId $cinemaId): ?Employe;

    /**
     * Trouve tous les emplois actifs d'un utilisateur
     *
     * @return array<Employe>
     */
    public function findAllActiveByUser(string $userUuid): array;

    /**
     * Vérifie si un emploi existe et est actif
     */
    public function exists(EmploiId $id): bool;
}
