<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Cinema;

use DB;
use Throwable;
use App\Domain\Cinema\Entities\Salle;
use App\Domain\Cinema\Enums\StatutSalle;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Infrastructure\Mappers\Cinema\SalleMapper;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Shared\ValueObjects\PaginatedCollection;
use App\Infrastructure\Database\Schemas\Cinema\SalleSchema;
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;
use App\Infrastructure\Database\Schemas\Cinema\CinemaSchema;
use App\Infrastructure\Database\Schemas\Cinema\SeanceSchema;
use App\Infrastructure\Repositories\Concerns\DispatchesEvents;
use App\Infrastructure\Database\Models\Cinema\Salle as SalleModel;

final class EloquentSalleRepository implements SalleRepositoryInterface
{
    use DispatchesEvents;

    public function save(Salle $salle): bool
    {

        try {
            $model = SalleModel::where(SalleSchema::ID, $salle->id->value)->first();
            if (!$model) {
                $model = new SalleModel;
                // Gestion Infrastructure : résolution cinema_db_id depuis UUID métier
                $cinema              = \App\Infrastructure\Database\Models\Cinema\Cinema::where('uuid', $salle->cinemaId->value)->first(['db_id']);
                $model->cinema_db_id = $cinema?->db_id;
            }
            SalleMapper::updateModel($model, $salle);

            $saved = $model->save();
            if ($saved) {
                $this->dispatchDomainEvents($salle);
            }

            return $saved;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function findById(SalleId $id): ?Salle
    {
        $model = SalleModel::where(SalleSchema::ID, $id->value)->first();

        return $model ? SalleMapper::toDomain($model) : null;
    }

    public function findByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $stringIds = array_map(fn (SalleId $id) => $id->value, $ids);

        $models = SalleModel::query()
            ->whereIn(SalleSchema::ID, $stringIds)
            ->get();

        $result = [];
        foreach ($models as $model) {
            $result[$model->getAttributes()[SalleSchema::ID]] = SalleMapper::toDomain($model);
        }

        return $result;
    }

    public function findByCinemaId(CinemaId $cinemaId): array
    {
        $models = SalleModel::query()
            ->where(SalleSchema::CINEMA_ID, $cinemaId->value)
            ->where(SalleSchema::STATUT, StatutSalle::ACTIVE->value)
            ->orderBy(SalleSchema::NOM)
            ->get();

        return $models->map(fn (SalleModel $model) => SalleMapper::toDomain($model))->toArray();
    }

    public function findAllActive(): array
    {
        $models = SalleModel::query()
            ->with(SalleModel::RELATION_CINEMA)
            ->where(SalleSchema::STATUT, StatutSalle::ACTIVE->value)
            ->orderBy(SalleSchema::NOM)
            ->get();

        return $models->map(fn (SalleModel $model) => SalleMapper::toDomain($model))->toArray();
    }

    // public function findByNumero(int $numero, CinemaId $cinemaId): ?Salle
    // {
    //     $model = SalleModel::query()
    //         ->where(SalleSchema::NUMERO, $numero)
    //         ->where(SalleSchema::CINEMA_ID, $cinemaId->value)
    //         ->first();

    //     return $model ? SalleMapper::toDomain($model) : null;
    // }

    // public function findWithEquipment(string $equipment): array
    // {
    //     $models = SalleModel::query()
    //         ->whereJsonContains(SalleSchema::EQUIPEMENTS, $equipment)
    //         ->get();

    //     return $models->map(fn (SalleModel $model) => SalleMapper::toDomain($model))->toArray();
    // }

    // public function findAccessible(): array
    // {
    //     $models = SalleModel::query()
    //         ->whereJsonContains(SalleSchema::ACCESSIBILITE . '->pmr', true)
    //         ->get();

    //     return $models->map(fn (SalleModel $model) => SalleMapper::toDomain($model))->toArray();
    // }

    public function findByCapacityRange(int $minCapacity, int $maxCapacity): array
    {
        $models = SalleModel::query()
            ->whereBetween(SalleSchema::CAPACITE_TOTALE, [$minCapacity, $maxCapacity])
            ->where(SalleSchema::STATUT, StatutSalle::ACTIVE->value)
            ->orderBy(SalleSchema::CAPACITE_TOTALE)
            ->get();

        return $models->map(fn (SalleModel $model) => SalleMapper::toDomain($model))->toArray();
    }

    public function delete(SalleId $id): bool
    {
        try {
            $model = SalleModel::find($id->value);

            return $model ? $model->delete() : false;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function exists(SalleId $id): bool
    {
        return SalleModel::where(SalleSchema::ID, $id->value)->exists();
    }

    public function findWithPagination(PaginationCriteria $criteria): PaginatedCollection
    {
        $builder = SalleModel::query();

        if ($criteria->filters) {
            $this->applyFilters($builder, $criteria->filters);
        }

        $builder->orderBy(SalleSchema::NOM);
        $paginated = $builder->paginate(perPage: $criteria->perPage, page: $criteria->page);

        $entities = array_map(
            fn (SalleModel $model) => SalleMapper::toDomain($model),
            $paginated->items()
        );

        return new PaginatedCollection(
            items: $entities,
            total: $paginated->total(),
            criteria: $criteria
        );
    }

    public function findWithPaginationAndCinemaNames(PaginationCriteria $criteria): array
    {
        // Cette méthode retourne directement des DTOs avec eager loading
        $offset = ($criteria->page - 1) * $criteria->perPage;

        $query = SalleModel::with('cinema');

        // Construire la query pour les filtres
        if ($criteria->filters) {
            $this->applyFilters($query, $criteria->filters);
        } else {
            // Par défaut, ne montrer que les salles actives si pas de filtre statut
            $query->where(SalleSchema::STATUT, StatutSalle::ACTIVE->value);
        }

        // Compter le total AVEC les filtres appliqués
        $totalQuery = clone $query;
        $total      = $totalQuery->count();

        // Appliquer pagination et tri
        $query->orderBy(SalleSchema::NOM)
            ->limit($criteria->perPage)
            ->offset($offset);

        $models = $query->get();

        $dtos = $models->map(function (SalleModel $model) {
            return [
                'uuid'               => $model->uuid,
                'nom'                => $model->nom,
                'capacite_totale'    => $model->capacite_totale,
                'nombre_rangees'     => $model->nombre_rangees,
                'places_par_rangee'  => $model->places_par_rangee,
                'places_standard'    => $model->places_standard,
                'places_pmr'         => $model->places_pmr,
                'qualite_projection' => $model->qualite_projection ?? [],
                'qualite_sonore'     => $model->qualite_sonore ?? [],
                'accessibilite_pmr'  => $model->accessibilite_pmr,
                'climatisation'      => $model->climatisation,
                'plan_salle'         => $model->plan_salle,
                'statut'             => $model->statut,
                'est_disponible'     => $model->statut === 'ACTIVE',
                'cinema_nom'         => $model->cinema?->nom,
            ];
        })->toArray();

        return [
            'items'    => $dtos,
            'total'    => $total,
            'page'     => $criteria->page,
            'per_page' => $criteria->perPage,
        ];
    }

    public function countByCinema(CinemaId $cinemaId): int
    {
        return SalleModel::query()
            ->where(SalleSchema::CINEMA_ID, $cinemaId->value)
            ->where(SalleSchema::STATUT, StatutSalle::ACTIVE->value)
            ->count();
    }

    public function nextIdentity(): SalleId
    {
        return SalleId::generate();
    }

    public function getTotalCapacityByCinema(CinemaId $cinemaId): int
    {
        return SalleModel::query()
            ->where(SalleSchema::CINEMA_ID, $cinemaId->value)
            ->where(SalleSchema::STATUT, StatutSalle::ACTIVE->value)
            ->sum(SalleSchema::CAPACITE_TOTALE) ?? 0;
    }

    public function findLargestByCinema(CinemaId $cinemaId): ?Salle
    {
        $model = SalleModel::query()
            ->where(SalleSchema::CINEMA_ID, $cinemaId->value)
            ->where(SalleSchema::STATUT, StatutSalle::ACTIVE->value)
            ->orderBy(SalleSchema::CAPACITE_TOTALE, 'desc')
            ->first();

        return $model ? SalleMapper::toDomain($model) : null;
    }

    public function hasAccessibleRoomByCinema(CinemaId $cinemaId): bool
    {
        return SalleModel::query()
            ->where(SalleSchema::CINEMA_ID, $cinemaId->value)
            ->where(SalleSchema::ACCESSIBILITE_PMR, true)
            ->where(SalleSchema::STATUT, StatutSalle::ACTIVE->value)
            ->exists();
    }

    public function findAllForSelect(): array
    {
        return SalleModel::query()
            ->select([
                SalleSchema::FULL_TABLE . '.' . SalleSchema::ID,
                SalleSchema::FULL_TABLE . '.' . SalleSchema::NOM,
                SalleSchema::FULL_TABLE . '.' . SalleSchema::CAPACITE_TOTALE,
                CinemaSchema::FULL_TABLE . '.' . CinemaSchema::NOM . ' as cinema_nom',
            ])
            ->join(CinemaSchema::FULL_TABLE, SalleSchema::CINEMA_KEY, '=', CinemaSchema::FULL_TABLE . '.' . CinemaSchema::PRIMARY_KEY)
            ->where(SalleSchema::FULL_TABLE . '.' . SalleSchema::STATUT, StatutSalle::ACTIVE->value)
            ->orderBy(CinemaSchema::FULL_TABLE . '.' . CinemaSchema::NOM)
            ->orderBy(SalleSchema::FULL_TABLE . '.' . SalleSchema::NOM)
            ->get()
            ->map(function ($salle) {
                return [
                    'id'              => $salle->uuid,
                    'nom'             => $salle->nom,
                    'capacite_totale' => $salle->capacite_totale,
                    'cinema_nom'      => $salle->cinema_nom,
                    'display_name'    => $salle->cinema_nom . ' - ' . $salle->nom . ' (' . $salle->capacite_totale . ' places)',
                ];
            })
            ->toArray();
    }

    public function findSallesWithSeancesForSelect(): array
    {
        return SalleModel::query()
            ->select([
                SalleSchema::FULL_TABLE . '.' . SalleSchema::ID,
                SalleSchema::FULL_TABLE . '.' . SalleSchema::NOM,
                SalleSchema::FULL_TABLE . '.' . SalleSchema::CAPACITE_TOTALE,
                CinemaSchema::FULL_TABLE . '.' . CinemaSchema::NOM . ' as cinema_nom',
            ])
            ->join(CinemaSchema::FULL_TABLE, SalleSchema::CINEMA_KEY, '=', CinemaSchema::FULL_TABLE . '.' . CinemaSchema::PRIMARY_KEY)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from(SeanceSchema::FULL_TABLE)
                    ->whereColumn(SeanceSchema::FULL_TABLE . '.' . SeanceSchema::SALLE_ID, SalleSchema::FULL_TABLE . '.' . SalleSchema::ID);
            })
            ->where(SalleSchema::FULL_TABLE . '.' . SalleSchema::STATUT, StatutSalle::ACTIVE->value)
            ->orderBy(CinemaSchema::FULL_TABLE . '.' . CinemaSchema::NOM)
            ->orderBy(SalleSchema::FULL_TABLE . '.' . SalleSchema::NOM)
            ->get()
            ->map(function ($salle) {
                return [
                    'id'              => $salle->uuid,
                    'nom'             => $salle->nom,
                    'capacite_totale' => $salle->capacite_totale,
                    'cinema_nom'      => $salle->cinema_nom,
                    'display_name'    => $salle->cinema_nom . ' - ' . $salle->nom . ' (' . $salle->capacite_totale . ' places)',
                ];
            })
            ->toArray();
    }

    private function applyFilters($builder, array $filters): void
    {
        if (isset($filters['cinema_id']) && !empty($filters['cinema_id'])) {
            $builder->where(SalleSchema::CINEMA_ID, $filters['cinema_id']);
        }

        if (isset($filters['statut']) && !empty($filters['statut'])) {
            // Convertir lowercase vers uppercase pour correspondre aux Enums
            $statutEnum = match ($filters['statut']) {
                'active'      => StatutSalle::ACTIVE->value,
                'maintenance' => StatutSalle::MAINTENANCE->value,
                'inactive'    => StatutSalle::HORS_SERVICE->value, // Mapper "inactive" vers HORS_SERVICE
                default       => StatutSalle::ACTIVE->value,
            };
            $builder->where(SalleSchema::STATUT, $statutEnum);
        }
        // Plus de filtre par défaut - montrer toutes les salles selon les filtres appliqués

        if (isset($filters['search']) && !empty($filters['search'])) {
            $builder->where(SalleSchema::NOM, 'ILIKE', '%' . $filters['search'] . '%');
        }

        if (isset($filters['accessibilite_pmr']) && $filters['accessibilite_pmr'] !== null) {
            $builder->where(SalleSchema::ACCESSIBILITE_PMR, $filters['accessibilite_pmr']);
        }

        if (isset($filters['climatisation']) && $filters['climatisation'] !== null) {
            $builder->where(SalleSchema::CLIMATISATION, $filters['climatisation']);
        }

        if (isset($filters['technologies']) && is_array($filters['technologies'])) {
            foreach ($filters['technologies'] as $tech) {
                $builder->whereJsonContains(SalleSchema::QUALITE_PROJECTION, $tech);
            }
        }

        if (isset($filters['min_capacity'])) {
            $builder->where(SalleSchema::CAPACITE_TOTALE, '>=', $filters['min_capacity']);
        }

        if (isset($filters['max_capacity'])) {
            $builder->where(SalleSchema::CAPACITE_TOTALE, '<=', $filters['max_capacity']);
        }
    }
}
