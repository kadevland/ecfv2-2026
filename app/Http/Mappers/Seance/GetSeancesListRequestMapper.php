<?php

declare(strict_types=1);

namespace App\Http\Mappers\Seance;

use Illuminate\Http\Request;
use App\Http\Mappers\BaseRequestMapper;
use App\Application\Seance\Queries\GetSeancesList\GetSeancesListQuery;

/**
 * Mapper pour convertir Request en GetSeancesListQuery
 */
final class GetSeancesListRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit une Request en GetSeancesListQuery
     */
    public static function toQuery(Request $request): GetSeancesListQuery
    {
        return new GetSeancesListQuery(
            page: $request->integer('page', 1),
            perPage: $request->integer('per_page', 20),
            search: $request->string('search')->toString() ?: null,
            filmUuid: $request->string('film_id')->toString() ?: null,
            salleUuid: $request->string('salle_id')->toString() ?: null,
            cinemaUuid: $request->string('cinema_id')->toString() ?: null,
            dateDebut: $request->string('date_debut')->toString() ?: null,
            dateFin: $request->string('date_fin')->toString() ?: null,
            version: $request->string('version')->toString() ?: null,
            technologies: $request->has('technologies') ? $request->input('technologies') : null,
            statut: $request->string('statut')->toString() ?: null,
            seancesAVenir: $request->has('seances_a_venir') ? $request->boolean('seances_a_venir') : null,
            sortBy: $request->string('sort_by', 'date_heure_debut')->toString(),
            sortDirection: $request->string('sort_direction', 'desc')->toString(),
        );
    }
}
