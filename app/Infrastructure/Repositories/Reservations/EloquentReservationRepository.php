<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Reservations;

use Throwable;
use App\Domain\User\ValueObjects\UserId;
use App\Domain\Cinema\ValueObjects\SeanceId;
use App\Domain\Reservations\Entities\Reservation;
use App\Domain\Shared\ValueObjects\PaginationCriteria;
use App\Domain\Reservations\ValueObjects\ReservationId;
use App\Domain\Shared\ValueObjects\PaginatedCollection;
use App\Infrastructure\Mappers\Reservations\ReservationMapper;
use App\Infrastructure\Repositories\Concerns\DispatchesEvents;
use App\Infrastructure\Database\Schemas\Reservations\ReservationSchema;
use App\Domain\Reservations\Repositories\ReservationRepositoryInterface;
use App\Infrastructure\Database\Models\Reservations\Reservation as ReservationModel;

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

    public function findByUserId(UserId $userId, array $filters = [], int $page = 1, int $perPage = 10): PaginatedCollection
    {
        $query = ReservationModel::query()
            ->where(ReservationSchema::USER_ID, $userId->value)
            ->orderBy(ReservationSchema::CREATED_AT, 'desc');

        // Appliquer les filtres
        if (!empty($filters['statut'])) {
            $query->where(ReservationSchema::STATUT, $filters['statut']);
        }

        // Pagination
        $total      = $query->count();
        $totalPages = (int) ceil($total / $perPage);

        $models = $query
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $items = $models->map(fn (ReservationModel $model) => ReservationMapper::toDomain($model))->toArray();

        $criteria = new PaginationCriteria(
            page: $page,
            perPage: $perPage,
            filters: $filters
        );

        return new PaginatedCollection(
            items: $items,
            total: $total,
            criteria: $criteria
        );
    }

    public function findBySeanceId(SeanceId $seanceId): array
    {
        $models = ReservationModel::query()
            ->where(ReservationSchema::SEANCE_ID, $seanceId->value)
            ->where(ReservationSchema::STATUT, '!=', 'annulee')
            ->get();

        return $models->map(fn (ReservationModel $model) => ReservationMapper::toDomain($model))->toArray();
    }

    public function findByNumero(string $numeroReservation): ?Reservation
    {
        $model = ReservationModel::query()
            ->where(ReservationSchema::NUMERO_RESERVATION, $numeroReservation)
            ->first();

        return $model ? ReservationMapper::toDomain($model) : null;
    }

    public function findByStatut(string $statut): array
    {
        $models = ReservationModel::query()
            ->where(ReservationSchema::STATUT, $statut)
            ->orderBy(ReservationSchema::CREATED_AT, 'desc')
            ->get();

        return $models->map(fn (ReservationModel $model) => ReservationMapper::toDomain($model))->toArray();
    }

    public function findExpired(): array
    {
        $models = ReservationModel::query()
            ->where(ReservationSchema::DATE_EXPIRATION, '<', now())
            ->where(ReservationSchema::STATUT, 'en_attente')
            ->get();

        return $models->map(fn (ReservationModel $model) => ReservationMapper::toDomain($model))->toArray();
    }

    public function findPending(): array
    {
        return $this->findByStatut('en_attente');
    }

    public function findConfirmed(): array
    {
        return $this->findByStatut('confirmee');
    }

    public function findByQrCode(string $qrCode): ?Reservation
    {
        $model = ReservationModel::query()
            ->where(ReservationSchema::QR_CODE, $qrCode)
            ->first();

        return $model ? ReservationMapper::toDomain($model) : null;
    }

    public function delete(ReservationId $id): bool
    {
        try {
            $model = ReservationModel::find($id->value);

            return $model ? $model->delete() : false;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function exists(ReservationId $id): bool
    {
        return ReservationModel::where(ReservationSchema::ID, $id->value)->exists();
    }

    public function nextIdentity(): ReservationId
    {
        return ReservationId::generate();
    }

    public function generateReservationNumber(): string
    {
        $prefix    = 'RES';
        $timestamp = now()->format('ymd');
        $random    = strtoupper(substr(md5(uniqid()), 0, 6));

        return $prefix . $timestamp . $random;
    }

    public function countBySeance(SeanceId $seanceId): int
    {
        return ReservationModel::query()
            ->where(ReservationSchema::SEANCE_ID, $seanceId->value)
            ->where(ReservationSchema::STATUT, '!=', 'annulee')
            ->count();
    }

    public function getReservedSeatsForSeance(SeanceId $seanceId): int
    {
        return ReservationModel::query()
            ->where(ReservationSchema::SEANCE_ID, $seanceId->value)
            ->where(ReservationSchema::STATUT, '!=', 'annulee')
            ->sum(ReservationSchema::NOMBRE_PLACES) ?? 0;
    }

    public function findWithPagination(PaginationCriteria $criteria): PaginatedCollection
    {
        $builder = ReservationModel::query();

        // JOIN avec la table des séances pour les filtres de date
        if ($criteria->filters && (isset($criteria->filters['date_from']) || isset($criteria->filters['date_to']))) {
            $builder->select('reservations.*')
                ->join('cinema.seances', 'reservations.seance_uuid', '=', 'cinema.seances.uuid');
        }

        if ($criteria->filters) {
            $this->applyFilters($builder, $criteria->filters);
        }

        $builder->orderBy(ReservationSchema::CREATED_AT, 'desc');
        $paginated = $builder->paginate(perPage: $criteria->perPage, page: $criteria->page);

        $entities = array_map(
            fn (ReservationModel $model) => ReservationMapper::toDomain($model),
            $paginated->items()
        );

        return new PaginatedCollection(
            items: $entities,
            total: $paginated->total(),
            criteria: $criteria
        );
    }

    public function findByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $models = ReservationModel::whereIn(ReservationSchema::ID, $ids)->get();

        $result = [];
        foreach ($models as $model) {
            // Retourner directement les modèles pour avoir accès aux timestamps
            $result[$model->{ReservationSchema::ID}] = $model;
        }

        return $result;
    }

    private function applyFilters($builder, array $filters): void
    {
        if (isset($filters['user_id'])) {
            $builder->where(ReservationSchema::USER_ID, $filters['user_id']);
        }

        if (isset($filters['seance_id'])) {
            $builder->where(ReservationSchema::SEANCE_ID, $filters['seance_id']);
        }

        if (isset($filters['statut'])) {
            $builder->where(ReservationSchema::STATUT, $filters['statut']);
        }

        if (isset($filters['numero_reservation'])) {
            $builder->where(ReservationSchema::NUMERO_RESERVATION, 'LIKE', '%' . $filters['numero_reservation'] . '%');
        }

        if (isset($filters['date_from'])) {
            $builder->whereDate('cinema.seances.date_heure_debut', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $builder->whereDate('cinema.seances.date_heure_debut', '<=', $filters['date_to']);
        }
    }
}
