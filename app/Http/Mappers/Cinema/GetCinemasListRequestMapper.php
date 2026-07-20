<?php

declare(strict_types=1);

namespace App\Http\Mappers\Cinema;

use Illuminate\Http\Request;
use App\Http\Mappers\BaseRequestMapper;
use App\Application\Cinema\Queries\GetCinemasList\GetCinemasListQuery;

/**
 * Mapper pour convertir Request en GetCinemasListQuery
 */
final class GetCinemasListRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit une Request HTTP en GetCinemasListQuery
     */
    public static function toQuery(Request $request): GetCinemasListQuery
    {
        // Récupération des paramètres de requête
        $location = self::sanitizeString($request->query('location'));
        $page     = self::toInt($request->query('page'), 1);
        $perPage  = self::toInt($request->query('per_page'), 20);

        // Construction des filtres depuis les paramètres de requête
        $filters = self::buildFilters($request);

        return new GetCinemasListQuery(
            location: $location,
            filters: !empty($filters) ? $filters : null,
            page: max(1, $page), // S'assurer que la page est au minimum 1
            perPage: min(100, max(5, $perPage)), // Limiter entre 5 et 100
        );
    }

    /**
     * Construit les filtres depuis la requête HTTP
     *
     * @return array<string, mixed>
     */
    private static function buildFilters(Request $request): array
    {
        $filters = [];

        // Filtre par statut actif
        if ($request->has('actif')) {
            $filters['actif'] = self::toBool($request->query('actif'));
        }

        // Filtre par ville
        if ($request->has('ville') && !empty($request->query('ville'))) {
            $filters['ville'] = self::sanitizeString($request->query('ville'));
        }

        // Filtre par pays
        if ($request->has('pays') && !empty($request->query('pays'))) {
            $filters['pays'] = self::sanitizeString($request->query('pays'));
        }

        // Tri
        if ($request->has('sort')) {
            $allowedSorts = ['nom', 'ville', 'created_at', 'updated_at'];
            $sort         = self::sanitizeString($request->query('sort'));

            if (in_array($sort, $allowedSorts, true)) {
                $filters['sort'] = $sort;

                // Direction du tri
                if ($request->has('direction')) {
                    $direction            = strtolower(self::sanitizeString($request->query('direction')));
                    $filters['direction'] = in_array($direction, ['asc', 'desc'], true) ? $direction : 'asc';
                }
            }
        }

        // Recherche textuelle
        if ($request->has('search') && !empty($request->query('search'))) {
            $filters['search'] = self::sanitizeString($request->query('search'));
        }

        return $filters;
    }
}
