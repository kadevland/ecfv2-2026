<?php

declare(strict_types=1);

namespace App\Domain\Users\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use App\Infrastructure\Database\Models\Auth\User;
use App\Domain\Shared\ValueObjects\PaginationCriteria;

interface UserRepositoryInterface
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function findWithPagination(PaginationCriteria $criteria): LengthAwarePaginator;

    public function findById(string $id): ?User;

    public function findByUuid(string $uuid): ?User;

    public function findByEmail(string $email): ?User;

    /**
     * @param array<string, mixed> $userData
     * @param array<string, mixed> $profileData
     */
    public function createUser(array $userData, array $profileData): User;

    /**
     * @param array<string, mixed> $userData
     * @param array<string, mixed> $profileData
     */
    public function updateUser(string $id, array $userData, array $profileData): bool;

    public function deleteUser(string $id): bool;

    public function findUserWithProfile(string $id): ?User;

    /**
     * Retourne tous les utilisateurs actifs pour les selects/dropdowns
     * Format optimisé : ['id' => 'uuid', 'label' => 'nom prénom (role)']
     *
     * @return array<array{id: string, label: string}>
     */
    public function findAllForSelect(): array;
}
