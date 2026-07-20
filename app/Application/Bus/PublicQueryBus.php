<?php

declare(strict_types=1);

namespace App\Application\Bus;

use Exception;
use App\Application\Contracts\Result;
use App\Application\Contracts\QueryInterface;
use App\Application\Contracts\QueryHandlerInterface;

/**
 * Bus de queries public dédié pour MongoDB
 * Sépare les queries publiques (MongoDB) des queries admin (PostgreSQL)
 */
final class PublicQueryBus
{
    /**
     * @var array<string, string> Mapping Query → Handler pour le côté public
     */
    private array $publicQueryMappings = [
        // Cinema Public Queries (MongoDB)
        \App\Application\Public\Cinema\Queries\GetPublicCinemasList\GetPublicCinemasListQuery::class => \App\Application\Public\Cinema\Queries\GetPublicCinemasList\GetPublicCinemasListQueryHandler::class,

        \App\Application\Public\Cinema\Queries\GetPublicCinemaDetail\GetPublicCinemaDetailQuery::class => \App\Application\Public\Cinema\Queries\GetPublicCinemaDetail\GetPublicCinemaDetailQueryHandler::class,

        // Film Public Queries (MongoDB)
        \App\Application\Public\Film\Queries\GetFilmsCatalog\GetFilmsCatalogQuery::class => \App\Application\Public\Film\Queries\GetFilmsCatalog\GetFilmsCatalogQueryHandler::class,

        \App\Application\Public\Film\Queries\GetFilmDetail\GetFilmDetailQuery::class => \App\Application\Public\Film\Queries\GetFilmDetail\GetFilmDetailQueryHandler::class,

        // Seance Public Queries (MongoDB)
        \App\Application\Public\Seance\Queries\GetSeancesByFilm\GetSeancesByFilmQuery::class => \App\Application\Public\Seance\Queries\GetSeancesByFilm\GetSeancesByFilmQueryHandler::class,
    ];

    public function ask(QueryInterface $query): Result
    {
        $queryClass = get_class($query);

        if (!isset($this->publicQueryMappings[$queryClass])) {
            return Result::error(
                'PUBLIC_QUERY_NOT_FOUND',
                "Aucun handler public trouvé pour: {$queryClass}"
            );
        }

        $handlerClass = $this->publicQueryMappings[$queryClass];

        try {
            /** @var QueryHandlerInterface $handler */
            $handler = app($handlerClass);

            return $handler->handle($query);
        } catch (Exception $e) {
            return Result::error(
                'PUBLIC_QUERY_EXECUTION_FAILED',
                "Erreur lors de l'exécution de la query publique: " . $e->getMessage()
            );
        }
    }

    /**
     * Ajouter dynamiquement un mapping Query → Handler
     */
    public function registerPublicQuery(string $queryClass, string $handlerClass): void
    {
        $this->publicQueryMappings[$queryClass] = $handlerClass;
    }
}
