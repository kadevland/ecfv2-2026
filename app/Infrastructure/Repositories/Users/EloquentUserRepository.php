<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Users;

use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Infrastructure\Database\Models\Auth\User;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Infrastructure\Database\Schemas\Auth\UserSchema;
use App\Domain\Users\Repositories\UserRepositoryInterface;
use App\Infrastructure\Database\Models\Profiles\UserProfil;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;

final readonly class EloquentUserRepository implements UserRepositoryInterface
{
    public function findWithPagination(PaginationCriteria $criteria): LengthAwarePaginator
    {
        $query = User::query()
            ->with([User::RELATION_PROFIL, User::RELATION_CREDENTIAL])
            ->orderBy($criteria->sortBy ?? 'created_at', $criteria->sortDirection ?? 'desc');

        $this->applyFilters($query, $criteria->filters);

        return $query->paginate(
            perPage: $criteria->perPage,
            page: $criteria->page
        );
    }

    public function findById(string $id): ?User
    {
        return User::query()
            ->with([User::RELATION_PROFIL, User::RELATION_CREDENTIAL])
            ->where(UserSchema::ID, $id)
            ->first();
    }

    public function findByUuid(string $uuid): ?User
    {
        return User::query()
            ->with([User::RELATION_PROFIL, User::RELATION_CREDENTIAL])
            ->where(UserSchema::ID, $uuid)
            ->first();
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()
            ->with([User::RELATION_PROFIL, User::RELATION_CREDENTIAL])
            ->whereHas(User::RELATION_CREDENTIAL, function ($query) use ($email) {
                $query->where('email', $email);
            })
            ->first();
    }

    public function createUser(array $userData, array $profileData): User
    {
        try {
            DB::beginTransaction();

            // Create user
            $user = User::create($userData);

            // Create profile with user relationship
            $profileData[UserProfilSchema::USER_KEY] = $user->db_id;
            $profileData[UserProfilSchema::USER_ID]  = $user->id;

            UserProfil::create($profileData);

            DB::commit();

            return $user->load(User::RELATION_PROFIL);

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateUser(string $id, array $userData, array $profileData, array $credentialData = []): bool
    {
        try {
            DB::beginTransaction();

            $user = $this->findById($id);
            if (!$user) {
                return false;
            }

            // Update user
            if (!empty($userData)) {
                $user->update($userData);
            }

            // Update profile
            if (!empty($profileData) && $user->{User::RELATION_PROFIL}) {
                $user->{User::RELATION_PROFIL}->update($profileData);
            }

            // Update credentials (email, etc.)
            if (!empty($credentialData) && $user->{User::RELATION_CREDENTIAL}) {
                $user->{User::RELATION_CREDENTIAL}->update($credentialData);
            }

            DB::commit();

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteUser(string $id): bool
    {
        try {
            DB::beginTransaction();

            $user = $this->findById($id);
            if (!$user) {
                return false;
            }

            // Soft delete profile first
            if ($user->{User::RELATION_PROFIL}) {
                $user->{User::RELATION_PROFIL}->delete();
            }

            // Soft delete user
            $user->delete();

            DB::commit();

            return true;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function findUserWithProfile(string $id): ?User
    {
        return $this->findById($id);
    }

    public function findAllForSelect(): array
    {
        return User::query()
            ->with([User::RELATION_PROFIL, User::RELATION_CREDENTIAL])
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (User $user) {
                $profil     = $user->{User::RELATION_PROFIL};
                $credential = $user->{User::RELATION_CREDENTIAL};

                $fullName = trim(($profil?->prenom ?? '') . ' ' . ($profil?->nom ?? ''));
                $email    = $credential?->email ?? '';
                $role     = $credential?->role ?? '';

                $label = $fullName ?: $email;
                if ($role) {
                    $label .= " ({$role})";
                }

                return [
                    'id'    => $user->id,
                    'label' => $label,
                ];
            })
            ->toArray();
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if (isset($filters['type'])) {
            $query->where(UserSchema::TYPE, $filters['type']);
        }

        if (isset($filters['est_actif'])) {
            $query->where(UserSchema::IS_ACTIVE, $filters['est_actif']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->whereHas(User::RELATION_CREDENTIAL, function (Builder $credentialQuery) use ($search) {
                    $credentialQuery->where('email', 'ILIKE', "%{$search}%");
                })
                    ->orWhereHas(User::RELATION_PROFIL, function (Builder $profilQuery) use ($search) {
                        $profilQuery->where(UserProfilSchema::PRENOM, 'ILIKE', "%{$search}%")
                            ->orWhere(UserProfilSchema::NOM, 'ILIKE', "%{$search}%");
                    });
            });
        }
    }
}
