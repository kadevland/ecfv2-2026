<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Public;

use App\Infrastructure\Database\Models\MongoDB\SeancePublic;
use App\Domain\Public\Repositories\SeancePublicRepositoryInterface;

final class MongoSeancePublicRepository implements SeancePublicRepositoryInterface
{
    public function findByFilmId(string $filmId, bool $futuresOnly = true, ?int $limit = null): array
    {
        $query = SeancePublic::where('film_id', $filmId);

        if ($futuresOnly) {
            $query->where('date_heure_debut', '>', now());
        }

        // Filtrer seulement les séances disponibles
        $query->where('statut', 'PROGRAMMEE')
            ->where('places_disponibles', '>', 0);

        $totalCount = $query->count();

        if ($limit) {
            $query->limit($limit);
        }

        $seances = $query->orderBy('date_heure_debut', 'asc')->get();

        // Récupérer le titre du film depuis la première séance ou un défaut
        $filmTitre = $seances->first()?->film_titre ?? 'Film inconnu';

        return [
            'seances'     => $seances->map(fn ($seance) => $seance->toArray())->toArray(),
            'film_titre'  => $filmTitre,
            'total_count' => $totalCount,
        ];
    }

    public function findByCinemaId(string $cinemaId, bool $futuresOnly = true, ?int $limit = null): array
    {
        $query = SeancePublic::where('cinema_id', $cinemaId);

        if ($futuresOnly) {
            $query->where('date_heure_debut', '>', now());
        }

        // Filtrer seulement les séances disponibles
        $query->where('statut', 'PROGRAMMEE')
            ->where('places_disponibles', '>', 0);

        $totalCount = $query->count();

        if ($limit) {
            $query->limit($limit);
        }

        $seances = $query->orderBy('date_heure_debut', 'asc')->get();

        // Récupérer le nom du cinéma depuis la première séance ou un défaut
        $cinemaNom = $seances->first()?->cinema_nom ?? 'Cinéma inconnu';

        return [
            'seances'     => $seances->map(fn ($seance) => $seance->toArray())->toArray(),
            'cinema_nom'  => $cinemaNom,
            'total_count' => $totalCount,
        ];
    }

    public function findById(string $seanceId): ?array
    {
        $seance = SeancePublic::where('seance_id', $seanceId)->first();

        return $seance?->toArray();
    }

    public function findAvailableSeances(array $filters = [], ?int $limit = null): array
    {
        $query = SeancePublic::where('statut', 'PROGRAMMEE')
            ->where('places_disponibles', '>', 0)
            ->where('date_heure_debut', '>', now());

        // Appliquer les filtres
        foreach ($filters as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            match ($key) {
                'film_id'      => $query->where('film_id', $value),
                'cinema_id'    => $query->where('cinema_id', $value),
                'salle_id'     => $query->where('salle_id', $value),
                'version'      => $query->where('version', $value),
                'date_from'    => $query->where('date_heure_debut', '>=', $value),
                'date_to'      => $query->where('date_heure_debut', '<=', $value),
                'technologies' => $query->whereIn('technologies', is_array($value) ? $value : [$value]),
                default        => null,
            };
        }

        $totalCount = $query->count();

        if ($limit) {
            $query->limit($limit);
        }

        $seances = $query->orderBy('date_heure_debut', 'asc')->get();

        return [
            'seances'     => $seances->map(fn ($seance) => $seance->toArray())->toArray(),
            'total_count' => $totalCount,
        ];
    }
}
