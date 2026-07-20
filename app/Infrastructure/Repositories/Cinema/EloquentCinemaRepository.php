<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Cinema;

use Log;
use Throwable;
use App\Domain\Cinema\Entities\Cinema;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Infrastructure\Mappers\Cinema\CinemaMapper;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Shared\ValueObjects\PaginatedCollection;
use App\Infrastructure\Database\Schemas\Cinema\CinemaSchema;
use App\Domain\Cinema\Repositories\CinemaRepositoryInterface;
use App\Infrastructure\Repositories\Concerns\DispatchesEvents;
use App\Infrastructure\Database\Models\Cinema\Cinema as CinemaModel;

final class EloquentCinemaRepository implements CinemaRepositoryInterface
{
    use DispatchesEvents;

    public function save(Cinema $cinema): bool
    {
        try {
            $model = CinemaModel::firstOrNew([CinemaSchema::ID => $cinema->id->value]);
            CinemaMapper::updateModel($model, $cinema);

            $saved = $model->save();
            if ($saved) {
                // Dispatch domain events après persistence réussie
                $this->dispatchDomainEvents($cinema);
            }

            return $saved;
        } catch (Throwable $e) {
            Log::error('Cinema save failed in repository', [
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
                'cinema_id'  => $cinema->id->value,
                'cinema_nom' => $cinema->nom,
            ]);

            return false;
        }
    }

    public function findById(CinemaId $id): ?Cinema
    {
        $model = CinemaModel::findByUuid($id->value);

        return $model ? CinemaMapper::toDomain($model) : null;
    }

    public function findByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $stringIds = array_map(fn (CinemaId $id) => $id->value, $ids);

        $models = CinemaModel::query()
            ->whereIn(CinemaSchema::ID, $stringIds)
            ->get();

        $result = [];
        foreach ($models as $model) {
            $result[$model->getAttributes()[CinemaSchema::ID]] = CinemaMapper::toDomain($model);
        }

        return $result;
    }

    public function findAllActive(): array
    {
        $models = CinemaModel::query()->active()
            ->get();

        return $models->map(fn (CinemaModel $model) => CinemaMapper::toDomain($model))
            ->toArray();
    }

    public function findByNom(string $nom): ?Cinema
    {
        $model = CinemaModel::query()->whereNom($nom)
            ->first();

        return $model ? CinemaMapper::toDomain($model) : null;
    }

    public function findByVille(string $ville): array
    {
        $models = CinemaModel::query()->whereVille($ville)
            ->get();

        return $models->map(fn (CinemaModel $model) => CinemaMapper::toDomain($model))
            ->toArray();
    }

    public function delete(CinemaId $id): bool
    {
        try {
            $model = CinemaModel::findByUuid($id->value);

            if (!$model) {
                return false;
            }

            return $model->delete();
        } catch (Throwable $e) {
            return false;
        }
    }

    public function exists(CinemaId $id): bool
    {
        return CinemaModel::where(CinemaSchema::ID, $id->value)->exists();
    }

    public function nextIdentity(): CinemaId
    {
        return CinemaId::generate();
    }

    public function findWithPagination(PaginationCriteria $criteria): PaginatedCollection
    {
        $builder = CinemaModel::query()->withCount(CinemaModel::RELATION_SALLES);

        // Apply filters from criteria
        if ($criteria->filters) {
            $this->applyFilters($builder, $criteria->filters);
        }

        $builder->orderBy(CinemaSchema::NOM);

        $paginated = $builder->paginate(
            perPage: $criteria->perPage,
            page: $criteria->page
        );

        $entities       = $paginated->items();
        $cinemaEntities = array_map(
            fn (CinemaModel $model) => CinemaMapper::toDomain($model),
            $entities
        );

        return new PaginatedCollection(
            items: $cinemaEntities,
            total: $paginated->total(),
            criteria: $criteria
        );
    }

    public function searchByLocation(string $location): array
    {
        $models = CinemaModel::query()->searchAdresse($location)
            ->get();

        return $models->map(fn (CinemaModel $model) => CinemaMapper::toDomain($model))
            ->toArray();
    }

    public function findByRegion(array $cities): array
    {
        $models = CinemaModel::query()->whereRegion($cities)
            ->get();

        return $models->map(fn (CinemaModel $model) => CinemaMapper::toDomain($model))
            ->toArray();
    }

    public function countActive(): int
    {
        return CinemaModel::query()->active()
            ->count();
    }

    public function findWithGpsCoordinates(): array
    {
        $models = CinemaModel::query()->hasGpsCoordinates()
            ->get();

        return $models->map(fn (CinemaModel $model) => CinemaMapper::toDomain($model))
            ->toArray();
    }

    public function findInFranceMetropolitaine(): array
    {
        $models = CinemaModel::query()->inFranceMetropolitaine()
            ->get();

        return $models->map(fn (CinemaModel $model) => CinemaMapper::toDomain($model))
            ->toArray();
    }

    public function findInBelgique(): array
    {
        $models = CinemaModel::query()->inBelgique()
            ->get();

        return $models->map(fn (CinemaModel $model) => CinemaMapper::toDomain($model))
            ->toArray();
    }

    public function findInGeographicArea(float $minLat, float $maxLat, float $minLng, float $maxLng): array
    {
        $models = CinemaModel::query()
            ->hasGpsCoordinates()
            ->whereLatitudeBetween($minLat, $maxLat)
            ->whereLongitudeBetween($minLng, $maxLng)
            ->get();

        return $models->map(fn (CinemaModel $model) => CinemaMapper::toDomain($model))
            ->toArray();
    }

    public function findAllForSelect(): array
    {
        return CinemaModel::query()
            ->select([
                CinemaSchema::ID,
                CinemaSchema::NOM,
                CinemaSchema::ADRESSE . '->' . CinemaSchema::ADRESSE_VILLE . ' as ville',
                CinemaSchema::PAYS,
            ])
            ->where(CinemaSchema::EST_ACTIF, true)
            ->orderBy(CinemaSchema::NOM)
            ->get()
            ->map(function ($cinema) {
                return (object) [
                    'id'           => $cinema->uuid,
                    'uuid'         => $cinema->uuid,
                    'nom'          => $cinema->nom,
                    'ville'        => $cinema->ville,
                    'pays'         => $cinema->pays?->value ?? '',
                    'display_name' => $cinema->nom . ' (' . $cinema->ville . ', ' . ($cinema->pays?->value ?? '') . ')',
                ];
            })
            ->toArray();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<CinemaModel> $builder
     * @param array<string, mixed> $filters
     */
    private function applyFilters($builder, array $filters): void
    {

        if (isset($filters['ville'])) {
            $builder->whereVille($filters['ville']);
        }

        if (isset($filters['code_postal'])) {
            $builder->whereCodePostal($filters['code_postal']);
        }

        if (isset($filters['pays'])) {
            $builder->wherePays($filters['pays']);
        }

        if (isset($filters['region']) && is_array($filters['region'])) {
            $builder->whereRegion($filters['region']);
        }

        if (isset($filters['location'])) {
            $builder->searchAdresse($filters['location']);
        }

        if (isset($filters['has_gps']) && $filters['has_gps']) {
            $builder->hasGpsCoordinates();
        }

        if (isset($filters['geographic_area']) && is_array($filters['geographic_area'])) {
            $area = $filters['geographic_area'];
            if (isset($area['min_lat'], $area['max_lat'], $area['min_lng'], $area['max_lng'])) {
                $builder->hasGpsCoordinates()
                    ->whereLatitudeBetween($area['min_lat'], $area['max_lat'])
                    ->whereLongitudeBetween($area['min_lng'], $area['max_lng']);
            }
        }
    }
}
