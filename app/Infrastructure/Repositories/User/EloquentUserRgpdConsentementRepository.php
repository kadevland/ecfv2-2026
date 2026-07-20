<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\User;

use Throwable;
use DateTimeInterface;
use App\Domain\User\ValueObjects\UserId;
use App\Domain\User\Entities\UserRgpdConsentement;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Shared\ValueObjects\PaginatedCollection;
use App\Domain\User\ValueObjects\UserRgpdConsentementId;
use App\Infrastructure\Mappers\User\UserRgpdConsentementMapper;
use App\Domain\User\Repositories\UserRgpdConsentementRepositoryInterface;
use App\Infrastructure\Database\Schemas\Profiles\UserRgpdConsentementSchema;
use App\Infrastructure\Database\Models\User\UserRgpdConsentement as UserRgpdConsentementModel;

final class EloquentUserRgpdConsentementRepository implements UserRgpdConsentementRepositoryInterface
{
    /** @phpstan-ignore-next-line method.contravariant */
    public function save(UserRgpdConsentement $consentement): bool
    {
        try {
            $model = UserRgpdConsentementModel::firstOrNew([
                UserRgpdConsentementSchema::USER_UUID         => $consentement->userUuid->value, // @phpstan-ignore-line
                UserRgpdConsentementSchema::TYPE_CONSENTEMENT => $consentement->typeConsentement, // @phpstan-ignore-line
            ]);
            UserRgpdConsentementMapper::updateModel($model, $consentement); // @phpstan-ignore-line

            return $model->save();
        } catch (Throwable $e) {
            // Log error in real implementation
            return false;
        }
    }

    /** @phpstan-ignore-next-line method.covariant */
    public function findById(UserRgpdConsentementId $id): ?UserRgpdConsentement
    {
        $model = UserRgpdConsentementModel::find($id->value);

        return $model ? UserRgpdConsentementMapper::toDomain($model) : null; // @phpstan-ignore-line
    }

    /**
     * @return array<UserRgpdConsentement>
     *
     * @phpstan-ignore-next-line
     */
    public function findByUserId(UserId $userId): array
    {
        $models = UserRgpdConsentementModel::query()
            ->where(UserRgpdConsentementSchema::USER_UUID, $userId->value)
            ->orderBy(UserRgpdConsentementSchema::DATE_CONSENTEMENT, 'desc')
            ->get();

        return $models->map(fn (UserRgpdConsentementModel $model) => UserRgpdConsentementMapper::toDomain($model)) // @phpstan-ignore-line
            ->toArray();
    }

    /** @phpstan-ignore-next-line method.covariant */
    public function findByUserIdAndType(UserId $userId, string $typeConsentement): ?UserRgpdConsentement
    {
        $model = UserRgpdConsentementModel::query()
            ->where(UserRgpdConsentementSchema::USER_UUID, $userId->value)
            ->where(UserRgpdConsentementSchema::TYPE_CONSENTEMENT, $typeConsentement)
            ->first();

        return $model ? UserRgpdConsentementMapper::toDomain($model) : null; // @phpstan-ignore-line
    }

    /**
     * @return array<UserRgpdConsentement>
     *
     * @phpstan-ignore-next-line
     */
    public function findActiveByUserId(UserId $userId): array
    {
        $models = UserRgpdConsentementModel::query()
            ->where(UserRgpdConsentementSchema::USER_UUID, $userId->value)
            ->where(UserRgpdConsentementSchema::CONSENTEMENT_DONNE, true)
            ->whereNull(UserRgpdConsentementSchema::DATE_RETRAIT)
            ->get();

        return $models->map(fn (UserRgpdConsentementModel $model) => UserRgpdConsentementMapper::toDomain($model)) // @phpstan-ignore-line
            ->toArray();
    }

    /**
     * @return array<string>
     */
    public function findUsersWithActiveConsent(string $typeConsentement): array
    {
        return UserRgpdConsentementModel::query()
            ->where(UserRgpdConsentementSchema::TYPE_CONSENTEMENT, $typeConsentement)
            ->where(UserRgpdConsentementSchema::CONSENTEMENT_DONNE, true)
            ->whereNull(UserRgpdConsentementSchema::DATE_RETRAIT)
            ->pluck(UserRgpdConsentementSchema::USER_UUID)
            ->toArray();
    }

    /**
     * @return array<UserRgpdConsentement>
     *
     * @phpstan-ignore-next-line
     */
    public function findByType(string $typeConsentement): array
    {
        $models = UserRgpdConsentementModel::query()
            ->where(UserRgpdConsentementSchema::TYPE_CONSENTEMENT, $typeConsentement)
            ->orderBy(UserRgpdConsentementSchema::DATE_CONSENTEMENT, 'desc')
            ->get();

        return $models->map(fn (UserRgpdConsentementModel $model) => UserRgpdConsentementMapper::toDomain($model)) // @phpstan-ignore-line
            ->toArray();
    }

    /**
     * @return array<UserRgpdConsentement>
     *
     * @phpstan-ignore-next-line
     */
    public function findConsentsBetween(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        $models = UserRgpdConsentementModel::query()
            ->whereBetween(UserRgpdConsentementSchema::DATE_CONSENTEMENT, [$startDate, $endDate])
            ->where(UserRgpdConsentementSchema::CONSENTEMENT_DONNE, true)
            ->get();

        return $models->map(fn (UserRgpdConsentementModel $model) => UserRgpdConsentementMapper::toDomain($model)) // @phpstan-ignore-line
            ->toArray();
    }

    /**
     * @return array<UserRgpdConsentement>
     *
     * @phpstan-ignore-next-line
     */
    public function findWithdrawnBetween(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        $models = UserRgpdConsentementModel::query()
            ->whereBetween(UserRgpdConsentementSchema::DATE_RETRAIT, [$startDate, $endDate])
            ->whereNotNull(UserRgpdConsentementSchema::DATE_RETRAIT)
            ->get();

        return $models->map(fn (UserRgpdConsentementModel $model) => UserRgpdConsentementMapper::toDomain($model)) // @phpstan-ignore-line
            ->toArray();
    }

    public function delete(UserRgpdConsentementId $id): bool
    {
        try {
            $model = UserRgpdConsentementModel::find($id->value);

            if (!$model) {
                return false;
            }

            return $model->delete();
        } catch (Throwable $e) {
            return false;
        }
    }

    public function deleteAllForUser(UserId $userId): bool
    {
        try {
            return UserRgpdConsentementModel::where(UserRgpdConsentementSchema::USER_UUID, $userId->value)
                ->delete() >= 0;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function exists(UserRgpdConsentementId $id): bool
    {
        return UserRgpdConsentementModel::where(UserRgpdConsentementSchema::ID, $id->value)->exists();
    }

    public function userHasActiveConsent(UserId $userId, string $typeConsentement): bool
    {
        return UserRgpdConsentementModel::query()
            ->where(UserRgpdConsentementSchema::USER_UUID, $userId->value)
            ->where(UserRgpdConsentementSchema::TYPE_CONSENTEMENT, $typeConsentement)
            ->where(UserRgpdConsentementSchema::CONSENTEMENT_DONNE, true)
            ->whereNull(UserRgpdConsentementSchema::DATE_RETRAIT)
            ->exists();
    }

    public function nextIdentity(): UserRgpdConsentementId
    {
        return UserRgpdConsentementId::generate();
    }

    public function findWithPagination(PaginationCriteria $criteria): PaginatedCollection
    {
        $builder = UserRgpdConsentementModel::query();

        // Apply filters from criteria
        if ($criteria->filters) {
            $this->applyFilters($builder, $criteria->filters);
        }

        $builder->orderBy(UserRgpdConsentementSchema::DATE_CONSENTEMENT, 'desc');

        $paginated = $builder->paginate(
            perPage: $criteria->perPage,
            page: $criteria->page
        );

        $entities             = $paginated->items();
        $consentementEntities = array_map(
            fn (UserRgpdConsentementModel $model) => UserRgpdConsentementMapper::toDomain($model), // @phpstan-ignore-line
            $entities
        );

        return new PaginatedCollection(
            items: $consentementEntities,
            total: $paginated->total(),
            criteria: $criteria
        );
    }

    public function count(): int
    {
        return UserRgpdConsentementModel::count();
    }

    public function countActiveByType(string $typeConsentement): int
    {
        return UserRgpdConsentementModel::query()
            ->where(UserRgpdConsentementSchema::TYPE_CONSENTEMENT, $typeConsentement)
            ->where(UserRgpdConsentementSchema::CONSENTEMENT_DONNE, true)
            ->whereNull(UserRgpdConsentementSchema::DATE_RETRAIT)
            ->count();
    }

    public function retractConsent(UserId $userId, string $typeConsentement): bool
    {
        try {
            return UserRgpdConsentementModel::query()
                ->where(UserRgpdConsentementSchema::USER_UUID, $userId->value)
                ->where(UserRgpdConsentementSchema::TYPE_CONSENTEMENT, $typeConsentement)
                ->where(UserRgpdConsentementSchema::CONSENTEMENT_DONNE, true)
                ->whereNull(UserRgpdConsentementSchema::DATE_RETRAIT)
                ->update([
                    UserRgpdConsentementSchema::CONSENTEMENT_DONNE => false,
                    UserRgpdConsentementSchema::DATE_RETRAIT       => now(),
                ]) > 0;
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * @param array<UserId> $userIds
     */
    public function bulkRetractConsents(array $userIds, string $typeConsentement): int
    {
        try {
            $userUuids = array_map(fn (UserId $userId) => $userId->value, $userIds);

            return UserRgpdConsentementModel::query()
                ->whereIn(UserRgpdConsentementSchema::USER_UUID, $userUuids)
                ->where(UserRgpdConsentementSchema::TYPE_CONSENTEMENT, $typeConsentement)
                ->where(UserRgpdConsentementSchema::CONSENTEMENT_DONNE, true)
                ->whereNull(UserRgpdConsentementSchema::DATE_RETRAIT)
                ->update([
                    UserRgpdConsentementSchema::CONSENTEMENT_DONNE => false,
                    UserRgpdConsentementSchema::DATE_RETRAIT       => now(),
                ]);
        } catch (Throwable $e) {
            return 0;
        }
    }

    /**
     * @return array<UserRgpdConsentement>
     *
     * @phpstan-ignore-next-line
     */
    public function findByIpAddress(string $ipAddress): array
    {
        $models = UserRgpdConsentementModel::query()
            ->where(UserRgpdConsentementSchema::IP_CONSENTEMENT, $ipAddress)
            ->orderBy(UserRgpdConsentementSchema::DATE_CONSENTEMENT, 'desc')
            ->get();

        return $models->map(fn (UserRgpdConsentementModel $model) => UserRgpdConsentementMapper::toDomain($model)) // @phpstan-ignore-line
            ->toArray();
    }

    /**
     * @return array<string, array{total: int, active: int, withdrawn: int}>
     */
    public function getConsentStatistics(): array
    {
        $statistics = [];

        // Get all consent types
        $types = UserRgpdConsentementModel::query()
            ->distinct()
            ->pluck(UserRgpdConsentementSchema::TYPE_CONSENTEMENT)
            ->toArray();

        foreach ($types as $type) {
            $total = UserRgpdConsentementModel::query()
                ->where(UserRgpdConsentementSchema::TYPE_CONSENTEMENT, $type)
                ->count();

            $active = UserRgpdConsentementModel::query()
                ->where(UserRgpdConsentementSchema::TYPE_CONSENTEMENT, $type)
                ->where(UserRgpdConsentementSchema::CONSENTEMENT_DONNE, true)
                ->whereNull(UserRgpdConsentementSchema::DATE_RETRAIT)
                ->count();

            $withdrawn = UserRgpdConsentementModel::query()
                ->where(UserRgpdConsentementSchema::TYPE_CONSENTEMENT, $type)
                ->whereNotNull(UserRgpdConsentementSchema::DATE_RETRAIT)
                ->count();

            $statistics[$type] = [
                'total'     => $total,
                'active'    => $active,
                'withdrawn' => $withdrawn,
            ];
        }

        return $statistics;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<UserRgpdConsentementModel> $builder
     * @param array<string, mixed> $filters
     */
    private function applyFilters(mixed $builder, array $filters): void
    {
        if (isset($filters['type_consentement'])) {
            $builder->where(UserRgpdConsentementSchema::TYPE_CONSENTEMENT, $filters['type_consentement']);
        }

        if (isset($filters['consentement_donne'])) {
            $builder->where(UserRgpdConsentementSchema::CONSENTEMENT_DONNE, $filters['consentement_donne']);
        }

        if (isset($filters['active_only']) && $filters['active_only']) {
            $builder->where(UserRgpdConsentementSchema::CONSENTEMENT_DONNE, true)
                ->whereNull(UserRgpdConsentementSchema::DATE_RETRAIT);
        }

        if (isset($filters['withdrawn_only']) && $filters['withdrawn_only']) {
            $builder->whereNotNull(UserRgpdConsentementSchema::DATE_RETRAIT);
        }

        if (isset($filters['user_uuid'])) {
            $builder->where(UserRgpdConsentementSchema::USER_UUID, $filters['user_uuid']);
        }

        if (isset($filters['ip_address'])) {
            $builder->where(UserRgpdConsentementSchema::IP_CONSENTEMENT, $filters['ip_address']);
        }

        if (isset($filters['date_from'])) {
            $builder->where(UserRgpdConsentementSchema::DATE_CONSENTEMENT, '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $builder->where(UserRgpdConsentementSchema::DATE_CONSENTEMENT, '<=', $filters['date_to']);
        }
    }
}
