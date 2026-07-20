<?php

declare(strict_types=1);

namespace App\Application\Cinema\Handlers;

use Exception;
use App\Application\Contracts\Result;
use App\Application\Contracts\QueryInterface;
use App\Application\Contracts\QueryHandlerInterface;
use App\Application\Cinema\Queries\GetCinemasForSelectQuery;
use App\Domain\Cinema\Repositories\CinemaRepositoryInterface;

/**
 * Handler pour récupérer la liste des cinémas pour un select
 */
final class GetCinemasForSelectHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly CinemaRepositoryInterface $cinemaRepository,
    ) {}

    public function handle(QueryInterface $query): Result
    {
        if (!$query instanceof GetCinemasForSelectQuery) {
            return Result::error('INVALID_QUERY', 'Query type not supported');
        }

        return $this->handleGetCinemasForSelect($query);
    }

    private function handleGetCinemasForSelect(GetCinemasForSelectQuery $query): Result
    {
        try {
            $cinemas = $this->cinemaRepository->findAllForSelect();

            return Result::success($cinemas);

        } catch (Exception $e) {
            return Result::error(
                error: 'SYSTEM_ERROR',
                message: 'Erreur lors de la récupération des cinémas : ' . $e->getMessage()
            );
        }
    }
}
