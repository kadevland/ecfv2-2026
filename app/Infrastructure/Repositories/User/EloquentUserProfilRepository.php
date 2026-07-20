<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\User;

use Throwable;
use DateTimeInterface;
use App\Domain\Shared\ValueObjects\Nom;
use App\Domain\User\Entities\UserProfil;
use App\Domain\User\ValueObjects\UserId;
use App\Domain\Shared\ValueObjects\Prenom;
use App\Domain\User\ValueObjects\UserProfilId;
use App\Infrastructure\Mappers\User\UserProfilMapper;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Shared\ValueObjects\PaginatedCollection;
use App\Domain\User\Repositories\UserProfilRepositoryInterface;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;
use App\Infrastructure\Database\Models\Profiles\UserProfil as UserProfilModel;

final class EloquentUserProfilRepository implements UserProfilRepositoryInterface
{
    public function save(UserProfil $userProfil): bool
    {
        try {
            $model = UserProfilModel::firstOrNew([UserProfilSchema::ID => $userProfil->id->value]);
            UserProfilMapper::updateModel($model, $userProfil);

            return $model->save();
        } catch (Throwable $e) {
            // Log error in real implementation
            return false;
        }
    }

    public function findById(UserProfilId $id): ?UserProfil
    {
        $model = UserProfilModel::find($id->value);

        return $model ? UserProfilMapper::toDomain($model) : null;
    }

    public function findByUserId(UserId $userId): ?UserProfil
    {
        $model = UserProfilModel::where(UserProfilSchema::USER_ID, $userId->value)->first();

        return $model ? UserProfilMapper::toDomain($model) : null;
    }

    public function findByUserUuid(string $userUuid): ?UserProfil
    {
        $model = UserProfilModel::where(UserProfilSchema::USER_ID, $userUuid)->first();

        return $model ? UserProfilMapper::toDomain($model) : null;
    }

    public function findByNomPrenom(Nom $nom, Prenom $prenom): array
    {
        $models = UserProfilModel::query()
            ->where(UserProfilSchema::NOM, $nom->toString())
            ->where(UserProfilSchema::PRENOM, $prenom->toString())
            ->get();

        return $models->map(fn (UserProfilModel $model) => UserProfilMapper::toDomain($model))
            ->toArray();
    }

    public function findNewsletterSubscribers(): array
    {
        $models = UserProfilModel::query()
            ->where(UserProfilSchema::NEWSLETTER, true)
            ->get();

        return $models->map(fn (UserProfilModel $model) => UserProfilMapper::toDomain($model))
            ->toArray();
    }

    public function findByVille(string $ville): array
    {
        $models = UserProfilModel::query()
            ->whereJsonContains(UserProfilSchema::ADRESSE . '->' . UserProfilSchema::ADRESSE_VILLE, $ville)
            ->get();

        return $models->map(fn (UserProfilModel $model) => UserProfilMapper::toDomain($model))
            ->toArray();
    }

    public function findByCodePostal(string $codePostal): array
    {
        $models = UserProfilModel::query()
            ->whereJsonContains(UserProfilSchema::ADRESSE . '->' . UserProfilSchema::ADRESSE_CODE_POSTAL, $codePostal)
            ->get();

        return $models->map(fn (UserProfilModel $model) => UserProfilMapper::toDomain($model))
            ->toArray();
    }

    public function delete(UserProfilId $id): bool
    {
        try {
            $model = UserProfilModel::find($id->value);

            if (!$model) {
                return false;
            }

            return $model->delete();
        } catch (Throwable $e) {
            return false;
        }
    }

    public function exists(UserProfilId $id): bool
    {
        return UserProfilModel::where(UserProfilSchema::ID, $id->value)->exists();
    }

    public function userHasProfil(UserId $userId): bool
    {
        return UserProfilModel::where(UserProfilSchema::USER_ID, $userId->value)->exists();
    }

    public function nextIdentity(): UserProfilId
    {
        return UserProfilId::generate();
    }

    public function findWithPagination(PaginationCriteria $criteria): PaginatedCollection
    {
        $builder = UserProfilModel::query();

        // Apply filters from criteria
        if ($criteria->filters) {
            $this->applyFilters($builder, $criteria->filters);
        }

        $builder->orderBy(UserProfilSchema::NOM)
            ->orderBy(UserProfilSchema::PRENOM);

        $paginated = $builder->paginate(
            perPage: $criteria->perPage,
            page: $criteria->page
        );

        $entities           = $paginated->items();
        $userProfilEntities = array_map(
            fn (UserProfilModel $model) => UserProfilMapper::toDomain($model),
            $entities
        );

        return new PaginatedCollection(
            items: $userProfilEntities,
            total: $paginated->total(),
            criteria: $criteria
        );
    }

    public function searchByNameOrAddress(string $search): array
    {
        $models = UserProfilModel::query()
            ->where(function ($query) use ($search) {
                $query->where(UserProfilSchema::NOM, 'ILIKE', "%{$search}%")
                    ->orWhere(UserProfilSchema::PRENOM, 'ILIKE', "%{$search}%")
                    ->orWhereJsonContains(UserProfilSchema::ADRESSE . '->' . UserProfilSchema::ADRESSE_VILLE, $search)
                    ->orWhereJsonContains(UserProfilSchema::ADRESSE . '->' . UserProfilSchema::ADRESSE_RUE, $search);
            })
            ->get();

        return $models->map(fn (UserProfilModel $model) => UserProfilMapper::toDomain($model))
            ->toArray();
    }

    public function count(): int
    {
        return UserProfilModel::count();
    }

    public function findByAgeRange(int $minAge, int $maxAge): array
    {
        $maxDate = now()->subYears($minAge)->toDateString();
        $minDate = now()->subYears($maxAge + 1)->toDateString();

        $models = UserProfilModel::query()
            ->whereBetween(UserProfilSchema::DATE_NAISSANCE, [$minDate, $maxDate])
            ->get();

        return $models->map(fn (UserProfilModel $model) => UserProfilMapper::toDomain($model))
            ->toArray();
    }

    public function findCreatedBetween(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        $models = UserProfilModel::query()
            ->whereBetween(UserProfilSchema::CREATED_AT, [$startDate, $endDate])
            ->get();

        return $models->map(fn (UserProfilModel $model) => UserProfilMapper::toDomain($model))
            ->toArray();
    }

    public function updateNewsletterSubscription(UserProfilId $id, bool $subscribed): bool
    {
        try {
            return UserProfilModel::where(UserProfilSchema::ID, $id->value)
                ->update([UserProfilSchema::NEWSLETTER => $subscribed]) > 0;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function findByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        // Retourner directement les modèles pour avoir accès à toutes les propriétés
        // incluant l'email depuis la relation user
        $models = UserProfilModel::whereIn(UserProfilSchema::ID, $ids)
            ->with('user') // Charger la relation pour avoir l'email
            ->get();

        $result = [];
        foreach ($models as $model) {
            $result[$model->uuid] = $model;
        }

        return $result;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<UserProfilModel> $builder
     * @param array<string, mixed> $filters
     */
    private function applyFilters($builder, array $filters): void
    {
        if (isset($filters['newsletter'])) {
            $builder->where(UserProfilSchema::NEWSLETTER, $filters['newsletter']);
        }

        if (isset($filters['ville'])) {
            $builder->whereJsonContains(UserProfilSchema::ADRESSE . '->' . UserProfilSchema::ADRESSE_VILLE, $filters['ville']);
        }

        if (isset($filters['code_postal'])) {
            $builder->whereJsonContains(UserProfilSchema::ADRESSE . '->' . UserProfilSchema::ADRESSE_CODE_POSTAL, $filters['code_postal']);
        }

        if (isset($filters['pays'])) {
            $builder->whereJsonContains(UserProfilSchema::ADRESSE . '->' . UserProfilSchema::ADRESSE_PAYS, $filters['pays']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $builder->where(function ($query) use ($search) {
                $query->where(UserProfilSchema::NOM, 'ILIKE', "%{$search}%")
                    ->orWhere(UserProfilSchema::PRENOM, 'ILIKE', "%{$search}%");
            });
        }

        if (isset($filters['age_min'], $filters['age_max'])) {
            $maxDate = now()->subYears($filters['age_min'])->toDateString();
            $minDate = now()->subYears($filters['age_max'] + 1)->toDateString();
            $builder->whereBetween(UserProfilSchema::DATE_NAISSANCE, [$minDate, $maxDate]);
        }

        if (isset($filters['created_after'])) {
            $builder->where(UserProfilSchema::CREATED_AT, '>=', $filters['created_after']);
        }

        if (isset($filters['created_before'])) {
            $builder->where(UserProfilSchema::CREATED_AT, '<=', $filters['created_before']);
        }
    }
}
