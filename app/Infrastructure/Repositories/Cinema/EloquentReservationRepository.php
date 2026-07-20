<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Cinema;

use Throwable;
use Ramsey\Uuid\Uuid;
use DateTimeInterface;
use App\Domain\Cinema\Entities\Reservation;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Domain\Cinema\ValueObjects\ReservationId;
use App\Domain\Cinema\ValueObjects\UtilisateurId;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Shared\ValueObjects\PaginatedCollection;
use App\Infrastructure\Mappers\Cinema\ReservationMapper;
use App\Infrastructure\Repositories\Concerns\DispatchesEvents;
use App\Infrastructure\Database\Schemas\Cinema\ReservationSchema;
use App\Domain\Cinema\Repositories\ReservationRepositoryInterface;
use App\Infrastructure\Database\Models\Cinema\Reservation as ReservationModel;

/**
 * Repository Eloquent complet pour les réservations
 */
final class EloquentReservationRepository implements ReservationRepositoryInterface
{
    use DispatchesEvents;

    public function save(Reservation $reservation): bool
    {
        try {
            $model = ReservationModel::firstOrNew([ReservationSchema::ID => $reservation->id->value]);
            ReservationMapper::updateModel($model, $reservation);

            $saved = $model->save();
            if ($saved) {
                $this->dispatchDomainEvents($reservation);
            }

            return $saved;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function findById(ReservationId $id): ?Reservation
    {
        $model = ReservationModel::where(ReservationSchema::ID, $id->value)->first();

        return $model ? ReservationMapper::toDomain($model) : null;
    }

    public function findBySeanceId(SeanceId $seanceId): array
    {
        $models = ReservationModel::where(ReservationSchema::SEANCE_ID, $seanceId->value)->get();

        return $models->map(fn ($model) => ReservationMapper::toDomain($model))->toArray();
    }

    public function findBySeanceIds(array $seanceIds): array
    {
        if (empty($seanceIds)) {
            return [];
        }

        $stringIds = array_map(fn (SeanceId $id) => $id->value, $seanceIds);

        $models = ReservationModel::query()
            ->whereIn(ReservationSchema::SEANCE_ID, $stringIds)
            ->get();

        $result = [];
        foreach ($models as $model) {
            $seanceIdValue = $model->getAttributes()[ReservationSchema::SEANCE_ID];
            if (!isset($result[$seanceIdValue])) {
                $result[$seanceIdValue] = [];
            }
            $result[$seanceIdValue][] = ReservationMapper::toDomain($model);
        }

        return $result;
    }

    public function findByUtilisateurId(UtilisateurId $utilisateurId): array
    {
        $models = ReservationModel::where(ReservationSchema::UTILISATEUR_ID, $utilisateurId->value)->get();

        return $models->map(fn ($model) => ReservationMapper::toDomain($model))->toArray();
    }

    public function findByDate(DateTimeInterface $date): array
    {
        $models = ReservationModel::whereDate(ReservationSchema::DATE_RESERVATION, $date)->get();

        return $models->map(fn ($model) => ReservationMapper::toDomain($model))->toArray();
    }

    public function findByDateRange(DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        $models = ReservationModel::whereBetween(ReservationSchema::DATE_RESERVATION, [$startDate, $endDate])->get();

        return $models->map(fn ($model) => ReservationMapper::toDomain($model))->toArray();
    }

    public function findByStatut(string $statut): array
    {
        $models = ReservationModel::where(ReservationSchema::STATUT, $statut)->get();

        return $models->map(fn ($model) => ReservationMapper::toDomain($model))->toArray();
    }

    public function findBySeanceAndUtilisateur(SeanceId $seanceId, UtilisateurId $utilisateurId): ?Reservation
    {
        $model = ReservationModel::where(ReservationSchema::SEANCE_ID, $seanceId->value)
            ->where(ReservationSchema::UTILISATEUR_ID, $utilisateurId->value)
            ->first();

        return $model ? ReservationMapper::toDomain($model) : null;
    }

    public function countBySeanceId(SeanceId $seanceId): int
    {
        return ReservationModel::where(ReservationSchema::SEANCE_ID, $seanceId->value)->count();
    }

    public function countPlacesBySeanceId(SeanceId $seanceId): int
    {
        return ReservationModel::where(ReservationSchema::SEANCE_ID, $seanceId->value)
            ->sum(ReservationSchema::NOMBRE_PLACES);
    }

    public function delete(ReservationId $id): bool
    {
        try {
            return ReservationModel::where(ReservationSchema::ID, $id->value)->delete() > 0;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function exists(ReservationId $id): bool
    {
        return ReservationModel::where(ReservationSchema::ID, $id->value)->exists();
    }

    public function findWithPagination(PaginationCriteria $criteria): PaginatedCollection
    {
        $query = ReservationModel::query();

        // Appliquer les filtres
        if (!empty($criteria->filters['search'])) {
            $search = $criteria->filters['search'];
            $query->where(ReservationSchema::ID, 'like', "%{$search}%");
        }

        if (!empty($criteria->filters['seance_id'])) {
            $query->where(ReservationSchema::SEANCE_ID, $criteria->filters['seance_id']);
        }

        if (!empty($criteria->filters['utilisateur_id'])) {
            $query->where(ReservationSchema::UTILISATEUR_ID, $criteria->filters['utilisateur_id']);
        }

        if (!empty($criteria->filters['statut'])) {
            $query->where(ReservationSchema::STATUT, $criteria->filters['statut']);
        }

        if (!empty($criteria->filters['date_debut'])) {
            $query->where(ReservationSchema::DATE_RESERVATION, '>=', $criteria->filters['date_debut']);
        }

        if (!empty($criteria->filters['date_fin'])) {
            $query->where(ReservationSchema::DATE_RESERVATION, '<=', $criteria->filters['date_fin']);
        }

        // Appliquer le tri
        $sortBy        = $criteria->filters['sort_by'] ?? ReservationSchema::DATE_RESERVATION;
        $sortDirection = $criteria->filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        // Paginer
        $paginated = $query->paginate($criteria->perPage, ['*'], 'page', $criteria->page);

        // Convertir en domaine
        $reservations       = $paginated->items();
        $domainReservations = array_map(fn ($model) => ReservationMapper::toDomain($model), $reservations);

        return new PaginatedCollection(
            items: $domainReservations,
            total: $paginated->total(),
            currentPage: $paginated->currentPage(),
            perPage: $paginated->perPage()
        );
    }

    public function nextIdentity(): ReservationId
    {
        return ReservationId::fromString(Uuid::uuid7()->toString());
    }

    public function findExpiredReservations(): array
    {
        $models = ReservationModel::where(ReservationSchema::STATUT, 'en_attente')
            ->where(ReservationSchema::DATE_EXPIRATION, '<', now())
            ->get();

        return $models->map(fn ($model) => ReservationMapper::toDomain($model))->toArray();
    }

    public function findPendingReservations(): array
    {
        $models = ReservationModel::where(ReservationSchema::STATUT, 'en_attente')->get();

        return $models->map(fn ($model) => ReservationMapper::toDomain($model))->toArray();
    }
}
