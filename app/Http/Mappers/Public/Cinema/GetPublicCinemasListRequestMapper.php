<?php

declare(strict_types=1);

namespace App\Http\Mappers\Public\Cinema;

use Illuminate\Http\Request;
use App\Http\Mappers\BaseRequestMapper;
use App\Application\Public\Cinema\Queries\GetPublicCinemasList\GetPublicCinemasListQuery;

/**
 * Mapper pour convertir Request en GetPublicCinemasListQuery (côté public)
 */
final class GetPublicCinemasListRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit une Request publique en Query MongoDB
     */
    public static function toQuery(Request $request): GetPublicCinemasListQuery
    {
        return new GetPublicCinemasListQuery(
            page: (int) $request->get('page', 1),
            perPage: 12, // Fixe pour le public
            location: $request->get('location'),
            filters: [] // Pas de filtres additionnels pour le public, location est géré séparément
        );
    }
}
