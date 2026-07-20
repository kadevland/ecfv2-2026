<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Cinema;

use Throwable;
use Ramsey\Uuid\Uuid;
use DateTimeInterface;
use App\Domain\Enums\StatutSeance;
use Illuminate\Support\Facades\Log;
use App\Domain\Cinema\Entities\Seance;
use App\Domain\Cinema\ValueObjects\FilmId;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Infrastructure\Mappers\Cinema\SeanceMapper;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Shared\ValueObjects\PaginatedCollection;
use App\Infrastructure\Database\Schemas\Cinema\SeanceSchema;
use App\Domain\Cinema\Repositories\SeanceRepositoryInterface;
use App\Infrastructure\Repositories\Concerns\DispatchesEvents;
use App\Infrastructure\Database\Models\Cinema\Seance as SeanceModel;

final class EloquentSeanceRepository implements SeanceRepositoryInterface
{
    use DispatchesEvents;

    public function save (Seance $seance) : bool
    {
        $seanceUuid = $seance->id->value;

        Log::info('💾 [Repository] Attempting to save seance', [
            'seance_uuid' => $seanceUuid,
            'operation'   => 'save',
        ]);

        try {
            // firstOrNew crée un modèle avec db_id null si non trouvé
            // ou retourne le modèle existant avec db_id rempli
            $model = SeanceModel::firstOrNew([SeanceSchema::ID => $seanceUuid]);

            // Eloquent sait si c'est nouveau grâce à exists
            $isNew = !$model->exists; // true si nouveau, false si existant

            Log::info('📋 [Repository] Seance model status', [
                'seance_uuid'  => $seanceUuid,
                'is_new'       => $isNew,
                'model_exists' => $model->exists,
            ]);

            // Mettre à jour les attributs
            SeanceMapper::updateModel($model, $seance);

            // save() fait automatiquement INSERT si nouveau ou UPDATE si existant
            // PostgreSQL n'évaluera la contrainte d'exclusion que contre les AUTRES lignes
            $saved = $model->save();

            Log::info('✅ [Repository] Seance saved to PostgreSQL', [
                'seance_uuid'  => $seanceUuid,
                'saved'        => $saved,
                'db_operation' => $isNew ? 'INSERT' : 'UPDATE',
            ]);

            if ($saved) {
                $eventCount = count($seance->getDomainEvents());
                Log::info('🎯 [Repository] Dispatching domain events', [
                    'seance_uuid' => $seanceUuid,
                    'event_count' => $eventCount,
                    'events'      => array_map(fn ($event) => get_class($event), $seance->getDomainEvents()),
                ]);

                $this->dispatchDomainEvents($seance);

                Log::info('📡 [Repository] Domain events dispatched successfully', [
                    'seance_uuid' => $seanceUuid,
                    'event_count' => $eventCount,
                ]);
            } else {
                Log::warning('⚠️ [Repository] Seance save returned false', [
                    'seance_uuid' => $seanceUuid,
                ]);
            }

            return $saved;
        } catch (Throwable $e) {
            // Log l'erreur pour debugging
            Log::error('💥 [Repository] Seance save error', [
                'seance_uuid'     => $seanceUuid,
                'error'           => $e->getMessage(),
                'exception_class' => get_class($e),
                'file'            => $e->getFile(),
                'line'            => $e->getLine(),
                'trace'           => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    public function findById (SeanceId $id) : ?Seance
    {
        $model = SeanceModel::where(SeanceSchema::ID, $id->value)->first();

        return $model ? SeanceMapper::toDomain($model) : null;
    }

    public function findByFilmId (FilmId $filmId) : array
    {
        $models = SeanceModel::query()
            ->where(SeanceSchema::FILM_ID, $filmId->value)
            ->orderBy(SeanceSchema::DATE_HEURE_DEBUT)
            ->get();

        return $models->map(fn (SeanceModel $model) => SeanceMapper::toDomain($model))
            ->toArray();
    }

    public function findBySalleId (SalleId $salleId) : array
    {
        $models = SeanceModel::query()
            ->where(SeanceSchema::SALLE_ID, $salleId->value)
            ->orderBy(SeanceSchema::DATE_HEURE_DEBUT)
            ->get();

        return $models->map(fn (SeanceModel $model) => SeanceMapper::toDomain($model))
            ->toArray();
    }

    public function findByCinemaId (CinemaId $cinemaId) : array
    {
        $models = SeanceModel::query()
            ->join('cinema.salles', 'cinema.seances.salle_id', '=', 'cinema.salles.id')
            ->where('cinema.salles.cinema_uuid', $cinemaId->value)
            ->select('cinema.seances.*')
            ->orderBy(SeanceSchema::DATE_SEANCE)
            ->get();

        return $models->map(fn (SeanceModel $model) => SeanceMapper::toDomain($model))
            ->toArray();
    }

    public function findByDate (DateTimeInterface $date) : array
    {
        $startOfDay = $date->format('Y-m-d 00:00:00');
        $endOfDay   = $date->format('Y-m-d 23:59:59');

        $models = SeanceModel::query()
            ->whereBetween(SeanceSchema::DATE_SEANCE, [$startOfDay, $endOfDay])
            ->orderBy(SeanceSchema::DATE_SEANCE)
            ->get();

        return $models->map(fn (SeanceModel $model) => SeanceMapper::toDomain($model))
            ->toArray();
    }

    public function findByDateRange (DateTimeInterface $startDate, DateTimeInterface $endDate) : array
    {
        $models = SeanceModel::query()
            ->whereBetween(SeanceSchema::DATE_SEANCE, [
                $startDate->format('Y-m-d H:i:s'),
                $endDate->format('Y-m-d H:i:s'),
            ])
            ->orderBy(SeanceSchema::DATE_SEANCE)
            ->get();

        return $models->map(fn (SeanceModel $model) => SeanceMapper::toDomain($model))
            ->toArray();
    }

    public function findUpcoming () : array
    {
        $now = now()->format('Y-m-d H:i:s');

        $models = SeanceModel::query()
            ->where(SeanceSchema::DATE_SEANCE, '>', $now)
            ->where(SeanceSchema::STATUT, 'programmee')
            ->orderBy(SeanceSchema::DATE_SEANCE)
            ->get();

        return $models->map(fn (SeanceModel $model) => SeanceMapper::toDomain($model))
            ->toArray();
    }

    public function findToday () : array
    {
        $today      = now()->format('Y-m-d');
        $startOfDay = $today . ' 00:00:00';
        $endOfDay   = $today . ' 23:59:59';

        $models = SeanceModel::query()
            ->whereBetween(SeanceSchema::DATE_SEANCE, [$startOfDay, $endOfDay])
            ->orderBy(SeanceSchema::DATE_SEANCE)
            ->get();

        return $models->map(fn (SeanceModel $model) => SeanceMapper::toDomain($model))
            ->toArray();
    }

    public function findByFilmAndDate (FilmId $filmId, DateTimeInterface $date) : array
    {
        $startOfDay = $date->format('Y-m-d 00:00:00');
        $endOfDay   = $date->format('Y-m-d 23:59:59');

        $models = SeanceModel::query()
            ->where(SeanceSchema::FILM_ID, $filmId->value)
            ->whereBetween(SeanceSchema::DATE_SEANCE, [$startOfDay, $endOfDay])
            ->orderBy(SeanceSchema::DATE_SEANCE)
            ->get();

        return $models->map(fn (SeanceModel $model) => SeanceMapper::toDomain($model))
            ->toArray();
    }

    public function findBySalleAndDate (SalleId $salleId, DateTimeInterface $date) : array
    {
        $startOfDay = $date->format('Y-m-d 00:00:00');
        $endOfDay   = $date->format('Y-m-d 23:59:59');

        $models = SeanceModel::query()
            ->where(SeanceSchema::SALLE_ID, $salleId->value)
            ->whereBetween(SeanceSchema::DATE_SEANCE, [$startOfDay, $endOfDay])
            ->orderBy(SeanceSchema::DATE_SEANCE)
            ->get();

        return $models->map(fn (SeanceModel $model) => SeanceMapper::toDomain($model))
            ->toArray();
    }

    public function findAvailableSeats (SeanceId $seanceId) : int
    {
        // This would require implementing reservation logic
        // For now, return a default value
        return 100;
    }

    public function delete (SeanceId $id) : bool
    {
        try {
            return SeanceModel::where(SeanceSchema::ID, $id->value)->delete() > 0;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function exists (SeanceId $id) : bool
    {
        return SeanceModel::where(SeanceSchema::ID, $id->value)->exists();
    }

    public function findWithPagination (PaginationCriteria $criteria) : PaginatedCollection
    {
        $query = SeanceModel::query();

        // Apply filters
        if (!empty($criteria->filters)) {
            $this->applyFilters($query, $criteria->filters);
        }

        //dump($criteria);

        // Apply sorting with column mapping
        $sortColumn = $this->mapSortColumn($criteria->sortBy);




        // Get total count before pagination
        $total = $query->count();
        //dd($sortColumn,$criteria->sortDirection);
        $query->orderBy($sortColumn, $criteria->sortDirection);

        // Apply pagination
        $offset = ($criteria->page - 1) * $criteria->perPage;
        $models = $query->offset($offset)
            ->limit($criteria->perPage)
            ->get();

        //dd($criteria,$query->toSql(),$total);

        // Convert to domain entities
        $items = $models->map(fn (SeanceModel $model) => SeanceMapper::toDomain($model))
            ->toArray();

        return new PaginatedCollection(
            items: $items,
            total: $total,
            criteria: $criteria
        );
    }

    public function nextIdentity () : SeanceId
    {
        return new SeanceId(Uuid::uuid4()->toString());
    }

    public function findConflictingSeances (
        SalleId $salleId,
        DateTimeInterface $startTime,
        DateTimeInterface $endTime,
        ?SeanceId $excludeSeanceId = null
    ) : array {
        $query = SeanceModel::query()
            ->where(SeanceSchema::SALLE_ID, $salleId->value);

        // Exclure directement la séance en cours de modification si fournie
        if ($excludeSeanceId !== null) {
            $query->where(SeanceSchema::ID, '!=', $excludeSeanceId->value);
        }

        // Utilisation simple et directe avec les colonnes DATE_HEURE_DEBUT et DATE_HEURE_FIN
        // Deux intervalles se chevauchent si : début1 < fin2 ET fin1 > début2
        $models = $query
            ->where(SeanceSchema::DATE_HEURE_DEBUT, '<', $endTime->format('Y-m-d H:i:s'))
            ->where(SeanceSchema::DATE_HEURE_FIN, '>', $startTime->format('Y-m-d H:i:s'))
            ->whereNotIn(SeanceSchema::STATUT, [StatutSeance::ANNULEE->value, StatutSeance::TERMINEE->value]) // Ignorer les séances annulées/terminées
            ->get();

        return $models->map(fn (SeanceModel $model) => SeanceMapper::toDomain($model))
            ->toArray();
    }

    public function findByIdsWithRelations (array $ids) : array
    {
        if (empty($ids)) {
            return [];
        }

        $models = SeanceModel::whereIn(SeanceSchema::ID, $ids)
            ->with(['film', 'salle.cinema']) // Eager load relations
            ->get();

        $result = [];
        foreach ($models as $model) {
            // Retourner directement les modèles Eloquent avec leurs relations chargées
            // Le handler extraira les données nécessaires
            $result[$model->{SeanceSchema::ID}] = $model;
        }

        return $result;
    }

    private function applyFilters ($query, array $filters) : void
    {
        foreach ($filters as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            //dump($key, $value);

            match ($key) {
                'film_id'         => $query->where(SeanceSchema::FILM_ID, $value),
                'salle_id'        => $query->where(SeanceSchema::SALLE_ID, $value),
                'cinema_id'       => $query->join('cinema.salles', 'cinema.seances.salle_id', '=', 'cinema.salles.id')
                    ->where('cinema.salles.cinema_uuid', $value)
                    ->select('cinema.seances.*'),
                'date_debut'      => $query->where(SeanceSchema::DATE_HEURE_DEBUT, '>=', $value . ' 00:00:00'),
                'date_fin'        => $query->where(SeanceSchema::DATE_HEURE_DEBUT, '<=', $value . ' 23:59:59'),
                'statut'          => $query->where(SeanceSchema::STATUT, $value),
                'version'         => $query->where(SeanceSchema::VERSION, $value),
                'technologies'    => is_array($value) && !empty($value)
                ? $query->where(function ($subQuery) use ($value) {
                        foreach ($value as $tech) {
                            $subQuery->orWhereJsonContains(SeanceSchema::OPTIONS_SUPPLEMENTAIRES, $tech);
                        }
                    })
                : null,
                'seances_a_venir' => $value ? $query->where(SeanceSchema::DATE_HEURE_DEBUT, '>', now()) : null,
                'placement_libre' => $query->where(SeanceSchema::PLACEMENT_LIBRE, $value),
                default           => null,
            };
        }
    }

    /**
     * Map sort column names to actual database columns
     */
    private function mapSortColumn (string $sortBy) : string
    {
        return match ($sortBy) {
            'id'               => SeanceSchema::PRIMARY_KEY,
            'date_heure_debut' => SeanceSchema::DATE_SEANCE,
            'date_heure_fin'   => SeanceSchema::HEURE_FIN,
            'film_titre'       => SeanceSchema::FILM_ID,
            'salle_nom'        => SeanceSchema::SALLE_ID,
            'version'          => SeanceSchema::VERSION,
            'statut'           => SeanceSchema::STATUT,
            'created_at'       => SeanceSchema::CREATED_AT,
            'updated_at'       => SeanceSchema::UPDATED_AT,
            default            => SeanceSchema::PRIMARY_KEY, // Default fallback
        };
    }
}
