<?php

declare(strict_types=1);

namespace App\Application\Users\Queries\GetClientProfils;

use Exception;
use App\Application\Contracts\Result;
use App\Application\Contracts\QueryInterface;
use App\Application\Contracts\QueryHandlerInterface;
use App\Application\Users\Queries\GetClientProfilsQuery;
use App\Infrastructure\Database\Models\Profiles\UserProfil;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;

final class GetClientProfilsQueryHandler implements QueryHandlerInterface
{
    public function handle(QueryInterface $query): Result
    {
        if (!$query instanceof GetClientProfilsQuery) {
            return Result::error(
                'INVALID_QUERY_TYPE',
                'Type de requête invalide'
            );
        }

        try {
            if (!$query->isValid()) {
                return Result::error(
                    'INVALID_QUERY',
                    'Données de requête invalides'
                );
            }

            // OPTIMISATION CQRS: Requête directe sur user_profils !
            $queryBuilder = UserProfil::query()
                ->where(UserProfilSchema::TYPE, 'client');

            // Appliquer les filtres
            $this->applyFilters($queryBuilder, $query);

            // Pagination avec tri par date de création (plus récent d'abord)
            $paginatedResults = $queryBuilder
                ->orderBy(UserProfilSchema::CREATED_AT, 'desc')
                ->paginate(
                    perPage: $query->perPage,
                    page: $query->page
                );

            return Result::success($paginatedResults);

        } catch (Exception $e) {
            return Result::error(
                'QUERY_FAILED',
                'Erreur lors de la récupération des profils clients: ' . $e->getMessage()
            );
        }
    }

    /** @param \Illuminate\Database\Eloquent\Builder<UserProfil> $queryBuilder */
    private function applyFilters(\Illuminate\Database\Eloquent\Builder $queryBuilder, GetClientProfilsQuery $query): void
    {
        // Filtre par recherche texte (nom, prénom, email)
        if ($query->search !== null) {
            $search = $query->search;
            $queryBuilder->where(function ($q) use ($search) {
                $q->where(UserProfilSchema::PRENOM, 'ILIKE', "%{$search}%")
                    ->orWhere(UserProfilSchema::NOM, 'ILIKE', "%{$search}%")
                    ->orWhere(UserProfilSchema::EMAIL, 'ILIKE', "%{$search}%");
            });
        }

        // Filtre par statut actif (nécessite JOIN avec users pour is_active)
        if ($query->estActif !== null) {
            $queryBuilder->whereHas('user', function ($userQuery) use ($query) {
                $userQuery->where('is_active', $query->estActif);
            });
        }
    }
}
