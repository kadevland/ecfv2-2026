<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Employees;

use App\Infrastructure\Database\Models\Employees\Emploi;
use App\Infrastructure\Database\Schemas\Employees\EmploiSchema;
use App\Domain\Employees\Repositories\EmploiRepositoryInterface;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;

final class EloquentEmploiRepository implements EmploiRepositoryInterface
{
    public function findActiveByUserUuid(string $userUuid): ?Emploi
    {
        return Emploi::query()
            ->join(
                UserProfilSchema::FULL_TABLE,
                EmploiSchema::FULL_TABLE . '.' . EmploiSchema::USER_PROFIL_KEY,
                '=',
                UserProfilSchema::FULL_TABLE . '.' . UserProfilSchema::PRIMARY_KEY
            )
            ->where(UserProfilSchema::FULL_TABLE . '.' . UserProfilSchema::USER_ID, $userUuid)
            ->where(EmploiSchema::FULL_TABLE . '.' . EmploiSchema::STATUT, 'ACTIF')
            ->select(EmploiSchema::FULL_TABLE . '.*')
            ->first();
    }

    public function save(Emploi $emploi): Emploi
    {
        $emploi->save();

        return $emploi;
    }

    public function findByUuid(string $uuid): ?Emploi
    {
        return Emploi::where(EmploiSchema::ID, $uuid)->first();
    }
}
