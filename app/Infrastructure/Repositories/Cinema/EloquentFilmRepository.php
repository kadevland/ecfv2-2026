<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Cinema;

use DB;
use Log;
use Throwable;
use DateTimeInterface;
use App\Domain\Cinema\Entities\Film;
use App\Domain\Cinema\ValueObjects\FilmId;
use App\Infrastructure\Mappers\Cinema\FilmMapper;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Shared\ValueObjects\PaginatedCollection;
use App\Infrastructure\Database\Schemas\Cinema\FilmSchema;
use App\Domain\Cinema\Repositories\FilmRepositoryInterface;
use App\Infrastructure\Database\Schemas\Cinema\SeanceSchema;
use App\Infrastructure\Repositories\Concerns\DispatchesEvents;
use App\Infrastructure\Database\Models\Cinema\Film as FilmModel;

final class EloquentFilmRepository implements FilmRepositoryInterface
{
    use DispatchesEvents;

    public function save(Film $film): bool
    {
        try {
            $model = FilmModel::firstOrNew([FilmSchema::ID => $film->id->value]);
            FilmMapper::updateModel($model, $film);

            $saved = $model->save();

            if ($saved) {
                $this->dispatchDomainEvents($film);
            }

            return $saved;
        } catch (Throwable $e) {
            Log::error('Repository save error', ['error' => $e->getMessage(), 'film_uuid' => $film->id->value]);

            return false;
        }
    }

    public function findById(FilmId $id): ?Film
    {
        $model = FilmModel::query()
            ->select($this->getStandardColumns())
            ->where(FilmSchema::ID, $id->value)
            ->first();

        return $model ? FilmMapper::toDomain($model) : null;
    }

    public function findByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $stringIds = array_map(fn (FilmId $id) => $id->value, $ids);

        $models = FilmModel::query()
            ->select($this->getStandardColumns())
            ->whereIn(FilmSchema::ID, $stringIds)
            ->get();

        $result = [];
        foreach ($models as $model) {
            $result[$model->getAttributes()[FilmSchema::ID]] = FilmMapper::toDomain($model);
        }

        return $result;
    }

    public function findAllActive(): array
    {
        $models = FilmModel::query()
            ->select($this->getStandardColumns())
            ->where(FilmSchema::EST_ACTIF, true)
            ->orderBy(FilmSchema::TITRE)
            ->get();

        return $models->map(fn (FilmModel $model) => FilmMapper::toDomain($model))
            ->toArray();
    }

    public function findInTheaters(): array
    {
        $now    = now();
        $models = FilmModel::query()
            ->select($this->getStandardColumns())
            ->where(FilmSchema::EST_ACTIF, true)
            ->where(FilmSchema::DATE_SORTIE, '<=', $now)
            ->where(function ($query) use ($now) {
                $query->whereNull(FilmSchema::DATE_FIN_EXPLOITATION)
                    ->orWhere(FilmSchema::DATE_FIN_EXPLOITATION, '>=', $now);
            })
            ->orderBy(FilmSchema::DATE_SORTIE, 'desc')
            ->get();

        return $models->map(fn (FilmModel $model) => FilmMapper::toDomain($model))
            ->toArray();
    }

    public function findByTitle(string $title): array
    {
        $models = FilmModel::query()
            ->select($this->getStandardColumns())
            ->where(FilmSchema::TITRE, 'ILIKE', "%{$title}%")
            ->orWhere(FilmSchema::TITRE_ORIGINAL, 'ILIKE', "%{$title}%")
            ->orderBy(FilmSchema::TITRE)
            ->get();

        return $models->map(fn (FilmModel $model) => FilmMapper::toDomain($model))
            ->toArray();
    }

    public function findByDirector(string $director): array
    {
        $models = FilmModel::query()
            ->select($this->getStandardColumns())
            ->whereJsonContains(FilmSchema::REALISATEURS, $director)
            ->orderBy(FilmSchema::DATE_SORTIE, 'desc')
            ->get();

        return $models->map(fn (FilmModel $model) => FilmMapper::toDomain($model))
            ->toArray();
    }

    public function findByGenre(string $genre): array
    {
        $models = FilmModel::query()
            ->select($this->getStandardColumns())
            ->whereJsonContains(FilmSchema::GENRES, $genre)
            ->where(FilmSchema::EST_ACTIF, true)
            ->orderBy(FilmSchema::DATE_SORTIE, 'desc')
            ->get();

        return $models->map(fn (FilmModel $model) => FilmMapper::toDomain($model))
            ->toArray();
    }

    public function findByClassification(string $classification): array
    {
        $models = FilmModel::query()
            ->select($this->getStandardColumns())
            ->where(FilmSchema::CLASSIFICATION, $classification)
            ->where(FilmSchema::EST_ACTIF, true)
            ->orderBy(FilmSchema::TITRE)
            ->get();

        return $models->map(fn (FilmModel $model) => FilmMapper::toDomain($model))
            ->toArray();
    }

    public function findUpcoming(): array
    {
        $models = FilmModel::query()
            ->select($this->getStandardColumns())
            ->where(FilmSchema::DATE_SORTIE, '>', now())
            ->where(FilmSchema::EST_ACTIF, true)
            ->orderBy(FilmSchema::DATE_SORTIE)
            ->get();

        return $models->map(fn (FilmModel $model) => FilmMapper::toDomain($model))
            ->toArray();
    }

    public function findReleasedBetween(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        $models = FilmModel::query()
            ->select($this->getStandardColumns())
            ->whereBetween(FilmSchema::DATE_SORTIE, [$startDate, $endDate])
            ->orderBy(FilmSchema::DATE_SORTIE, 'desc')
            ->get();

        return $models->map(fn (FilmModel $model) => FilmMapper::toDomain($model))
            ->toArray();
    }

    public function findTopRated(int $limit = 10): array
    {
        $models = FilmModel::query()
            ->select($this->getStandardColumns())
            ->whereNotNull(FilmSchema::NOTE_MOYENNE_AVIS)
            ->where(FilmSchema::NOMBRE_AVIS, '>', 0)
            ->orderBy(FilmSchema::NOTE_MOYENNE_AVIS, 'desc')
            ->orderBy(FilmSchema::NOMBRE_AVIS, 'desc')
            ->limit($limit)
            ->get();

        return $models->map(fn (FilmModel $model) => FilmMapper::toDomain($model))
            ->toArray();
    }

    public function findByMinimumRating(float $minRating): array
    {
        $models = FilmModel::query()
            ->select($this->getStandardColumns())
            ->where(FilmSchema::NOTE_MOYENNE_AVIS, '>=', $minRating)
            ->where(FilmSchema::NOMBRE_AVIS, '>', 0)
            ->orderBy(FilmSchema::NOTE_MOYENNE_AVIS, 'desc')
            ->get();

        return $models->map(fn (FilmModel $model) => FilmMapper::toDomain($model))
            ->toArray();
    }

    public function searchByText(string $searchText): array
    {
        $models = FilmModel::query()
            ->select($this->getStandardColumns())
            ->where(function ($query) use ($searchText) {
                $query->where(FilmSchema::TITRE, 'ILIKE', "%{$searchText}%")
                    ->orWhere(FilmSchema::TITRE_ORIGINAL, 'ILIKE', "%{$searchText}%")
                    ->orWhereJsonContains(FilmSchema::REALISATEURS, $searchText)
                    ->orWhereJsonContains(FilmSchema::ACTEURS_PRINCIPAUX, $searchText)
                    ->orWhere(FilmSchema::SYNOPSIS, 'ILIKE', "%{$searchText}%");
            })
            ->orderBy(FilmSchema::TITRE)
            ->get();

        return $models->map(fn (FilmModel $model) => FilmMapper::toDomain($model))
            ->toArray();
    }

    public function findAllForSelect(): array
    {
        return FilmModel::query()
            ->select([
                FilmSchema::ID,
                FilmSchema::TITRE,
                FilmSchema::DATE_SORTIE,
                FilmSchema::DUREE_MINUTES,
                FilmSchema::CLASSIFICATION,
            ])
            ->where(FilmSchema::EST_ACTIF, true)
            ->orderBy(FilmSchema::TITRE)
            ->get()
            ->map(function ($film) {
                return [
                    'id'                 => $film->{FilmSchema::ID},
                    'titre'              => $film->{FilmSchema::TITRE},
                    'annee_sortie'       => $film->{FilmSchema::DATE_SORTIE},
                    'duree_minutes'      => $film->{FilmSchema::DUREE_MINUTES},
                    'classification_age' => $film->{FilmSchema::CLASSIFICATION},
                    'display_name'       => $film->{FilmSchema::TITRE} . ' (' . $film->{FilmSchema::DATE_SORTIE} . ') - ' . $film->{FilmSchema::DUREE_MINUTES} . 'min',
                ];
            })
            ->toArray();
    }

    public function findFilmsWithSeancesForSelect(): array
    {
        return FilmModel::query()
            ->select([
                FilmSchema::ID,
                FilmSchema::TITRE,
                FilmSchema::DATE_SORTIE,
                FilmSchema::DUREE_MINUTES,
                FilmSchema::CLASSIFICATION,
            ])
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from(SeanceSchema::FULL_TABLE)
                    ->whereColumn(SeanceSchema::FULL_TABLE . '.' . SeanceSchema::FILM_ID, FilmSchema::FULL_TABLE . '.' . FilmSchema::ID);
            })
            ->where(FilmSchema::EST_ACTIF, true)
            ->orderBy(FilmSchema::TITRE)
            ->get()
            ->map(function ($film) {
                return [
                    'id'                 => $film->{FilmSchema::ID},
                    'titre'              => $film->{FilmSchema::TITRE},
                    'annee_sortie'       => $film->{FilmSchema::DATE_SORTIE},
                    'duree_minutes'      => $film->{FilmSchema::DUREE_MINUTES},
                    'classification_age' => $film->{FilmSchema::CLASSIFICATION},
                    'display_name'       => $film->{FilmSchema::TITRE} . ' (' . $film->{FilmSchema::DATE_SORTIE} . ') - ' . $film->{FilmSchema::DUREE_MINUTES} . 'min',
                ];
            })
            ->toArray();
    }

    public function findByDurationRange(int $minMinutes, int $maxMinutes): array
    {
        $models = FilmModel::query()
            ->select($this->getStandardColumns())
            ->whereBetween(FilmSchema::DUREE_MINUTES, [$minMinutes, $maxMinutes])
            ->where(FilmSchema::EST_ACTIF, true)
            ->orderBy(FilmSchema::DUREE_MINUTES)
            ->get();

        return $models->map(fn (FilmModel $model) => FilmMapper::toDomain($model))
            ->toArray();
    }

    public function findByLanguage(string $language): array
    {
        $models = FilmModel::query()
            ->select($this->getStandardColumns())
            ->where(FilmSchema::LANGUE_ORIGINALE, $language)
            ->where(FilmSchema::EST_ACTIF, true)
            ->orderBy(FilmSchema::TITRE)
            ->get();

        return $models->map(fn (FilmModel $model) => FilmMapper::toDomain($model))
            ->toArray();
    }

    public function delete(FilmId $id): bool
    {
        try {
            $model = FilmModel::find($id->value);

            if (!$model) {
                return false;
            }

            return $model->delete();
        } catch (Throwable $e) {
            return false;
        }
    }

    public function exists(FilmId $id): bool
    {
        return FilmModel::where(FilmSchema::ID, $id->value)->exists();
    }

    public function nextIdentity(): FilmId
    {
        return FilmId::generate();
    }

    public function findWithPagination(PaginationCriteria $criteria): PaginatedCollection
    {
        $builder = FilmModel::query()
            ->select($this->getStandardColumns());

        // Apply filters from criteria
        if ($criteria->filters) {
            $this->applyFilters($builder, $criteria->filters);
        }

        if ($criteria->sortBy) {
            $builder->orderBy($criteria->sortBy, $criteria->sortDirection ?? 'asc');
        } else {
            $builder->orderBy(FilmSchema::PRIMARY_KEY);
        }

        // $builder->orderBy(FilmSchema::TITRE);

        $paginated = $builder->paginate(
            perPage: $criteria->perPage,
            page: $criteria->page
        );

        $entities = $paginated->items();

        $filmEntities = array_map(
            fn (FilmModel $model) => FilmMapper::toDomain($model),
            $entities
        );

        return new PaginatedCollection(
            items: $filmEntities,
            total: $paginated->total(),
            criteria: $criteria
        );
    }

    public function countActive(): int
    {
        return FilmModel::where(FilmSchema::EST_ACTIF, true)->count();
    }

    public function countInTheaters(): int
    {
        $now = now();

        return FilmModel::query()
            ->where(FilmSchema::EST_ACTIF, true)
            ->where(FilmSchema::DATE_SORTIE, '<=', $now)
            ->where(function ($query) use ($now) {
                $query->whereNull(FilmSchema::DATE_FIN_EXPLOITATION)
                    ->orWhere(FilmSchema::DATE_FIN_EXPLOITATION, '>=', $now);
            })
            ->count();
    }

    public function updateRating(FilmId $id, float $newRating): bool
    {
        try {
            $model = FilmModel::find($id->value);
            if (!$model) {
                return false;
            }

            $model->updateAverageRating($newRating);

            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function findEndingSoon(int $days = 7): array
    {
        $endDate = now()->addDays($days);
        $models  = FilmModel::query()
            ->select($this->getStandardColumns())
            ->where(FilmSchema::EST_ACTIF, true)
            ->whereNotNull(FilmSchema::DATE_FIN_EXPLOITATION)
            ->where(FilmSchema::DATE_FIN_EXPLOITATION, '<=', $endDate)
            ->where(FilmSchema::DATE_FIN_EXPLOITATION, '>=', now())
            ->orderBy(FilmSchema::DATE_FIN_EXPLOITATION)
            ->get();

        return $models->map(fn (FilmModel $model) => FilmMapper::toDomain($model))
            ->toArray();
    }

    public function findMostPopular(int $limit = 10): array
    {
        $models = FilmModel::query()
            ->select($this->getStandardColumns())
            ->where(FilmSchema::NOMBRE_AVIS, '>', 0)
            ->orderBy(FilmSchema::NOMBRE_AVIS, 'desc')
            ->orderBy(FilmSchema::NOTE_MOYENNE_AVIS, 'desc')
            ->limit($limit)
            ->get();

        return $models->map(fn (FilmModel $model) => FilmMapper::toDomain($model))
            ->toArray();
    }

    public function findWithPoster(): array
    {
        $models = FilmModel::query()
            ->select($this->getStandardColumns())
            ->whereNotNull(FilmSchema::AFFICHE_URL)
            ->where(FilmSchema::EST_ACTIF, true)
            ->orderBy(FilmSchema::TITRE)
            ->get();

        return $models->map(fn (FilmModel $model) => FilmMapper::toDomain($model))
            ->toArray();
    }

    public function findWithTrailer(): array
    {
        $models = FilmModel::query()
            ->select($this->getStandardColumns())
            ->whereNotNull(FilmSchema::BANDE_ANNONCE_URL)
            ->where(FilmSchema::EST_ACTIF, true)
            ->orderBy(FilmSchema::TITRE)
            ->get();

        return $models->map(fn (FilmModel $model) => FilmMapper::toDomain($model))
            ->toArray();
    }

    public function findRecent(int $days = 30): array
    {
        $startDate = now()->subDays($days);
        $models    = FilmModel::query()
            ->select($this->getStandardColumns())
            ->where(FilmSchema::DATE_SORTIE, '>=', $startDate)
            ->where(FilmSchema::EST_ACTIF, true)
            ->orderBy(FilmSchema::DATE_SORTIE, 'desc')
            ->get();

        return $models->map(fn (FilmModel $model) => FilmMapper::toDomain($model))
            ->toArray();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<FilmModel> $builder
     * @param array<string, mixed> $filters
     */
    private function applyFilters($builder, array $filters): void
    {
        if (isset($filters['est_actif'])) {
            $builder->where(FilmSchema::EST_ACTIF, $filters['est_actif']);
        }

        if (isset($filters['in_theaters']) && $filters['in_theaters']) {
            $now = now();
            $builder->where(FilmSchema::EST_ACTIF, true)
                ->where(FilmSchema::DATE_SORTIE, '<=', $now)
                ->where(function ($query) use ($now) {
                    $query->whereNull(FilmSchema::DATE_FIN_EXPLOITATION)
                        ->orWhere(FilmSchema::DATE_FIN_EXPLOITATION, '>=', $now);
                });
        }

        if (isset($filters['genre'])) {
            $builder->whereJsonContains(FilmSchema::GENRES, $filters['genre']);
        }

        if (isset($filters['genres'])) {
            if (is_array($filters['genres'])) {
                $builder->where(function ($query) use ($filters) {
                    foreach ($filters['genres'] as $genre) {
                        $query->orWhereJsonContains(FilmSchema::GENRES, $genre);
                    }
                });
            } else {
                $builder->whereJsonContains(FilmSchema::GENRES, $filters['genres']);
            }
        }

        if (isset($filters['classification'])) {
            $builder->where(FilmSchema::CLASSIFICATION, $filters['classification']);
        }

        if (isset($filters['director'])) {
            $builder->whereJsonContains(FilmSchema::REALISATEURS, $filters['director']);
        }

        if (isset($filters['min_rating'])) {
            $builder->where(FilmSchema::NOTE_MOYENNE_AVIS, '>=', $filters['min_rating']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $builder->where(function ($query) use ($search) {
                $query->where(FilmSchema::TITRE, 'ILIKE', "%{$search}%")
                    ->orWhere(FilmSchema::TITRE_ORIGINAL, 'ILIKE', "%{$search}%")
                    ->orWhereJsonContains(FilmSchema::REALISATEURS, $search)
                    ->orWhereJsonContains(FilmSchema::ACTEURS_PRINCIPAUX, $search);
            });
        }

        if (isset($filters['min_duration'])) {
            $builder->where(FilmSchema::DUREE_MINUTES, '>=', $filters['min_duration']);
        }

        if (isset($filters['max_duration'])) {
            $builder->where(FilmSchema::DUREE_MINUTES, '<=', $filters['max_duration']);
        }

        if (isset($filters['language'])) {
            $builder->where(FilmSchema::LANGUE_ORIGINALE, $filters['language']);
        }

        if (isset($filters['has_poster']) && $filters['has_poster']) {
            $builder->whereNotNull(FilmSchema::AFFICHE_URL);
        }

        if (isset($filters['has_trailer']) && $filters['has_trailer']) {
            $builder->whereNotNull(FilmSchema::BANDE_ANNONCE_URL);
        }
    }

    /**
     * Get standard columns for Film queries
     *
     * @return array<string>
     */
    private function getStandardColumns(): array
    {
        return [
            FilmSchema::PRIMARY_KEY,
            FilmSchema::ID,
            FilmSchema::TITRE,
            FilmSchema::TITRE_ORIGINAL,
            FilmSchema::SYNOPSIS,
            FilmSchema::GENRES,
            FilmSchema::DUREE_MINUTES,
            FilmSchema::DATE_SORTIE,
            FilmSchema::PAYS_ORIGINE,
            FilmSchema::LANGUE_ORIGINALE,
            FilmSchema::CLASSIFICATION,
            FilmSchema::REALISATEURS,
            FilmSchema::ACTEURS_PRINCIPAUX,
            FilmSchema::PRODUCTEUR,
            FilmSchema::AFFICHE_URL,
            FilmSchema::BANDE_ANNONCE_URL,
            FilmSchema::IMAGES_ADDITIONNELLES,
            FilmSchema::NOTE_CRITIQUE,
            FilmSchema::NOTE_PUBLIC,
            FilmSchema::NOTE_MOYENNE_AVIS,
            FilmSchema::NOMBRE_AVIS,
            FilmSchema::SOUS_TITRES,
            FilmSchema::DATE_FIN_EXPLOITATION,
            FilmSchema::STATUT,
            FilmSchema::EST_ACTIF,
            FilmSchema::METADONNEES_TECHNIQUES,
            FilmSchema::CREATED_AT,
            FilmSchema::UPDATED_AT,
        ];
    }
}
