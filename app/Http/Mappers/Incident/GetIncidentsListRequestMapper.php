<?php

declare(strict_types=1);

namespace App\Http\Mappers\Incident;

use Illuminate\Http\Request;
use App\Domain\Enums\TypeIncident;
use App\Domain\Enums\StatutIncident;
use App\Domain\Enums\SeveriteIncident;
use App\Application\Employees\Queries\GetIncidentsList\GetIncidentsListQuery;

final class GetIncidentsListRequestMapper
{
    /**
     * Convertit une requête HTTP en GetIncidentsListQuery
     */
    public static function toQuery(Request $request): GetIncidentsListQuery
    {

        // Pour l'instant on utilise un UUID fixe pour tester
        $cinemaUuid = $request->input('cinema_uuid', '01940725-9d48-7a82-b5e3-cb9f12345678');

        $statut = null;
        if ($request->has('statut') && $request->input('statut') !== '') {
            $statut = StatutIncident::tryFrom($request->input('statut'));
        }

        $severite = null;
        if ($request->has('severite') && $request->input('severite') !== '') {
            $severite = SeveriteIncident::tryFrom($request->input('severite'));
        }

        $type = null;
        if ($request->has('type') && $request->input('type') !== '') {
            $type = TypeIncident::tryFrom($request->input('type'));
        }

        return new GetIncidentsListQuery(
            cinemaUuid: $cinemaUuid,
            emploiUuid: $request->input('emploi_uuid'),
            salleUuid: $request->input('salle_uuid'),
            statut: $statut,
            severite: $severite,
            type: $type,
            openOnly: $request->boolean('open_only'),
            criticalOnly: $request->boolean('critical_only'),
            recentDays: $request->input('recent_days') ? (int) $request->input('recent_days') : null,
            limit: $request->input('limit') ? (int) $request->input('limit') : null,
            sortBy: $request->input('sort_by', 'created_at'),
            sortDirection: $request->input('sort_direction', 'desc'),
        );
    }
}
