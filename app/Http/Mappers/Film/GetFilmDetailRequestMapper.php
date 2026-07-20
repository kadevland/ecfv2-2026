<?php

declare(strict_types=1);

namespace App\Http\Mappers\Film;

use Illuminate\Http\Request;
use App\Http\Mappers\BaseRequestMapper;
use App\Application\Film\Queries\GetFilmDetail\GetFilmDetailQuery;

/**
 * Mapper pour convertir Request en GetFilmDetailQuery
 */
final class GetFilmDetailRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit une Request en GetFilmDetailQuery pour l'affichage
     */
    public static function toQuery(Request $request, string $filmUuid): GetFilmDetailQuery
    {
        return new GetFilmDetailQuery(
            filmUuid: $filmUuid,
            includeSeances: true,
            includeAvis: true
        );
    }

    /**
     * Convertit une Request en GetFilmDetailQuery pour l'édition
     */
    public static function toQueryForEdit(Request $request, string $filmUuid): GetFilmDetailQuery
    {
        return new GetFilmDetailQuery(
            filmUuid: $filmUuid,
            includeSeances: false,
            includeAvis: false
        );
    }
}
