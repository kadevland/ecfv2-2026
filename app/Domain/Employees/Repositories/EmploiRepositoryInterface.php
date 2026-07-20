<?php

declare(strict_types=1);

namespace App\Domain\Employees\Repositories;

use App\Infrastructure\Database\Models\Employees\Emploi;

interface EmploiRepositoryInterface
{
    public function findActiveByUserUuid(string $userUuid): ?Emploi;

    public function save(Emploi $emploi): Emploi;

    public function findByUuid(string $uuid): ?Emploi;
}
