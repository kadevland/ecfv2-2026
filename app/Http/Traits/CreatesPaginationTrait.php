<?php

declare(strict_types=1);

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Application\Contracts\PaginatedResponseInterface;

/**
 * Trait pour créer des LengthAwarePaginator depuis les Use Cases
 *
 * Permet de convertir automatiquement les réponses paginées framework-agnostic
 * vers des LengthAwarePaginator Laravel pour les vues
 */
trait CreatesPaginationTrait
{
    /**
     * Crée un LengthAwarePaginator depuis une réponse paginée Use Case
     *
     * @param PaginatedResponseInterface $response Réponse du Use Case
     * @param Request $request Request HTTP courante
     * @param string $pageName Nom du paramètre de pagination (default: 'page')
     * @return LengthAwarePaginator<int, mixed>
     */
    protected function createPaginator(
        PaginatedResponseInterface $response,
        Request $request,
        string $pageName = 'page'
    ): LengthAwarePaginator {
        // 1. Créer le LengthAwarePaginator
        /** @var LengthAwarePaginator<int, mixed> $paginator */
        $paginator = new LengthAwarePaginator(
            $response->getItems(),
            $response->getTotal(),
            $response->getPerPage(),
            $response->getPage(),
            [
                'path'     => $request->url(),
                'pageName' => $pageName,
            ]
        );

        // 2. Préserver tous les query parameters pour la pagination
        $paginator->appends($request->query());

        return $paginator;
    }

    /**
     * Crée un LengthAwarePaginator avec un path personnalisé
     *
     * Utile pour les cas où l'URL de pagination diffère de l'URL courante
     *
     * @return LengthAwarePaginator<int, mixed>
     */
    protected function createPaginatorWithPath(
        PaginatedResponseInterface $response,
        Request $request,
        string $customPath,
        string $pageName = 'page'
    ): LengthAwarePaginator {
        /** @var LengthAwarePaginator<int, mixed> $paginator */
        $paginator = new LengthAwarePaginator(
            $response->getItems(),
            $response->getTotal(),
            $response->getPerPage(),
            $response->getPage(),
            [
                'path'     => $customPath,
                'pageName' => $pageName,
            ]
        );

        $paginator->appends($request->query());

        return $paginator;
    }

    /**
     * Crée un LengthAwarePaginator en excluant certains paramètres de query
     *
     * Utile pour exclure des paramètres sensibles ou temporaires
     *
     * @param array<int, string> $excludeParams
     * @return LengthAwarePaginator<int, mixed>
     */
    protected function createPaginatorExcluding(
        PaginatedResponseInterface $response,
        Request $request,
        array $excludeParams = [],
        string $pageName = 'page'
    ): LengthAwarePaginator {
        /** @var LengthAwarePaginator<int, mixed> $paginator */
        $paginator = new LengthAwarePaginator(
            $response->getItems(),
            $response->getTotal(),
            $response->getPerPage(),
            $response->getPage(),
            [
                'path'     => $request->url(),
                'pageName' => $pageName,
            ]
        );

        // Ajouter tous les paramètres sauf ceux exclus
        $queryParams = collect($request->query())
            ->except($excludeParams)
            ->toArray();

        $paginator->appends($queryParams);

        return $paginator;
    }
}
