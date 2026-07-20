<?php

declare(strict_types=1);

namespace App\Domain\User\Repositories;

use DateTimeInterface;
use App\Domain\User\ValueObjects\UserId;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Shared\ValueObjects\PaginatedCollection;
use App\Domain\User\ValueObjects\UserRgpdConsentementId;
use App\Infrastructure\Database\Models\User\UserRgpdConsentement;

interface UserRgpdConsentementRepositoryInterface
{
    /**
     * Save user RGPD consent entity (create or update)
     */
    public function save(UserRgpdConsentement $consentement): bool;

    /**
     * Find RGPD consent by ID
     */
    public function findById(UserRgpdConsentementId $id): ?UserRgpdConsentement;

    /**
     * Find consents by user ID
     *
     * @return array<UserRgpdConsentement>
     */
    public function findByUserId(UserId $userId): array;

    /**
     * Find consent by user ID and type
     */
    public function findByUserIdAndType(UserId $userId, string $typeConsentement): ?UserRgpdConsentement;

    /**
     * Find active consents by user ID
     *
     * @return array<UserRgpdConsentement>
     */
    public function findActiveByUserId(UserId $userId): array;

    /**
     * Find users with active consent for specific type
     *
     * @return array<string> User UUIDs
     */
    public function findUsersWithActiveConsent(string $typeConsentement): array;

    /**
     * Find consents by type
     *
     * @return array<UserRgpdConsentement>
     */
    public function findByType(string $typeConsentement): array;

    /**
     * Find consents given in date range
     *
     * @return array<UserRgpdConsentement>
     */
    public function findConsentsBetween(DateTimeInterface $startDate, DateTimeInterface $endDate): array;

    /**
     * Find withdrawn consents in date range
     *
     * @return array<UserRgpdConsentement>
     */
    public function findWithdrawnBetween(DateTimeInterface $startDate, DateTimeInterface $endDate): array;

    /**
     * Delete RGPD consent
     */
    public function delete(UserRgpdConsentementId $id): bool;

    /**
     * Delete all consents for a user (RGPD deletion)
     */
    public function deleteAllForUser(UserId $userId): bool;

    /**
     * Check if consent exists
     */
    public function exists(UserRgpdConsentementId $id): bool;

    /**
     * Check if user has active consent for type
     */
    public function userHasActiveConsent(UserId $userId, string $typeConsentement): bool;

    /**
     * Find consents with pagination
     */
    public function findWithPagination(PaginationCriteria $criteria): PaginatedCollection;

    /**
     * Get total count of consents
     */
    public function count(): int;

    /**
     * Get count of active consents by type
     */
    public function countActiveByType(string $typeConsentement): int;

    /**
     * Generate next identity for new consent
     */
    public function nextIdentity(): UserRgpdConsentementId;

    /**
     * Retract consent for user and type
     */
    public function retractConsent(UserId $userId, string $typeConsentement): bool;

    /**
     * Bulk retract consents for multiple users
     *
     * @param array<UserId> $userIds
     */
    public function bulkRetractConsents(array $userIds, string $typeConsentement): int;

    /**
     * Find consents by IP address (for audit)
     *
     * @return array<UserRgpdConsentement>
     */
    public function findByIpAddress(string $ipAddress): array;

    /**
     * Get consent statistics by type
     *
     * @return array<string, array{total: int, active: int, withdrawn: int}>
     */
    public function getConsentStatistics(): array;
}
