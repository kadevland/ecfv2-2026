<?php

declare(strict_types=1);

namespace App\Http\Mappers\Film;

use Illuminate\Http\Request;
use App\Http\Mappers\BaseRequestMapper;
use App\Application\Film\Queries\GetFilmsList\GetFilmsListQuery;

/**
 * Mapper pour convertir Request en GetFilmsListQuery
 */
final class GetFilmsListRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit une Request en GetFilmsListQuery
     */
    public static function toQuery(Request $request): GetFilmsListQuery
    {
        // Transformer le genre unique en array si présent
        $genres = null;
        if ($request->has('genre') && !empty($request->input('genre'))) {
            $genres = [$request->input('genre')];
        }

        return new GetFilmsListQuery(
            page: $request->integer('page', 1),
            perPage: $request->integer('per_page', 20),
            search: $request->string('search')->toString() ?: null,
            genres: $genres,
            classification: $request->string('classification')->toString() ?: null,
            enSalles: $request->has('en_salles') ? $request->boolean('en_salles') : null,
            prochainement: $request->has('prochainement') ? $request->boolean('prochainement') : null,
            // sortBy: $request->string('sort_by', 'titre')->toString(),
            // sortDirection: $request->string('sort_direction', 'desc')->toString(),
        );
    }
}
