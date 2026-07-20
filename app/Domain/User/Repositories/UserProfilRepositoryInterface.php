<?php

declare(strict_types=1);

namespace App\Domain\User\Repositories;

use DateTimeInterface;
use App\Domain\Shared\ValueObjects\Nom;
use App\Domain\User\Entities\UserProfil;
use App\Domain\User\ValueObjects\UserId;
use App\Domain\Shared\ValueObjects\Prenom;
use App\Domain\User\ValueObjects\UserProfilId;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Shared\ValueObjects\PaginatedCollection;

interface UserProfilRepositoryInterface
{
    /**
     * Save user profil entity (create or update)
     */
    public function save(UserProfil $userProfil): bool;

    /**
     * Find user profil by ID
     */
    public function findById(UserProfilId $id): ?UserProfil;

    /**
     * Find user profil by user ID
     */
    public function findByUserId(UserId $userId): ?UserProfil;

    /**
     * Find user profil by user UUID
     */
    public function findByUserUuid(string $userUuid): ?UserProfil;

    /**
     * Find user profils by name
     *
     * @return array<UserProfil>
     */
    public function findByNomPrenom(Nom $nom, Prenom $prenom): array;

    /**
     * Find user profils subscribed to newsletter
     *
     * @return array<UserProfil>
     */
    public function findNewsletterSubscribers(): array;

    /**
     * Find user profils by city
     *
     * @return array<UserProfil>
     */
    public function findByVille(string $ville): array;

    /**
     * Find user profils by postal code
     *
     * @return array<UserProfil>
     */
    public function findByCodePostal(string $codePostal): array;

    /**
     * Delete user profil
     */
    public function delete(UserProfilId $id): bool;

    /**
     * Check if user profil exists
     */
    public function exists(UserProfilId $id): bool;

    /**
     * Check if user has profil
     */
    public function userHasProfil(UserId $userId): bool;

    /**
     * Find user profils with pagination
     */
    public function findWithPagination(PaginationCriteria $criteria): PaginatedCollection;

    /**
     * Search user profils by name or address
     *
     * @return array<UserProfil>
     */
    public function searchByNameOrAddress(string $search): array;

    /**
     * Get total count of user profils
     */
    public function count(): int;

    /**
     * Generate next identity for new user profil
     */
    public function nextIdentity(): UserProfilId;

    /**
     * Find user profils by age range
     *
     * @return array<UserProfil>
     */
    public function findByAgeRange(int $minAge, int $maxAge): array;

    /**
     * Find user profils created in date range
     *
     * @return array<UserProfil>
     */
    public function findCreatedBetween(DateTimeInterface $startDate, DateTimeInterface $endDate): array;

    /**
     * Update newsletter subscription status
     */
    public function updateNewsletterSubscription(UserProfilId $id, bool $subscribed): bool;

    /**
     * Find multiple user profils by their IDs
     *
     * @param array<string> $ids Array of UUID strings
     * @return array<string, UserProfil> Keyed by UUID
     */
    public function findByIds(array $ids): array;
}
