<?php

declare(strict_types=1);

namespace App\Application\Users\Handlers;

use Exception;
use App\Application\Contracts\Result;
use App\Application\Users\Queries\GetUsersQuery;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Users\Repositories\UserRepositoryInterface;

final readonly class GetUsersHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function handle(GetUsersQuery $query): Result
    {
        try {
            $filters = $this->buildFilters($query);

            $criteria = new PaginationCriteria(
                page: $query->page,
                perPage: $query->perPage,
                filters: $filters,
            );

            $paginatedCollection = $this->userRepository->findWithPagination($criteria);

            return Result::success($paginatedCollection);

        } catch (Exception $e) {
            return Result::error(
                'QUERY_FAILED',
                'Erreur lors de la récupération des utilisateurs: ' . $e->getMessage()
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildFilters(GetUsersQuery $query): array
    {
        $filters = [];

        if ($query->type !== null) {
            $filters['type'] = $query->type;
        }

        if ($query->estActif !== null) {
            $filters['est_actif'] = $query->estActif;
        }

        if ($query->search !== null) {
            $filters['search'] = $query->search;
        }

        return $filters;
    }
}
