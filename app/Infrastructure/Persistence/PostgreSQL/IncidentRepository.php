<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\PostgreSQL;

use Carbon\Carbon;
use App\Domain\Enums\TypeIncident;
use App\Domain\Enums\StatutIncident;
use App\Domain\Enums\SeveriteIncident;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Domain\Employees\Entities\Incident;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Domain\Employees\ValueObjects\EmploiId;
use App\Domain\Employees\ValueObjects\IncidentId;
use App\Infrastructure\Mappers\Employees\IncidentMapper;
use App\Domain\Employees\Repositories\IncidentRepositoryInterface;
use App\Infrastructure\Database\Models\Employees\Incident as EloquentIncident;

final class IncidentRepository implements IncidentRepositoryInterface
{
    public function findById(IncidentId $id): ?Incident
    {
        $model = EloquentIncident::where('uuid', $id->toString())->first();

        if (!$model) {
            return null;
        }

        return IncidentMapper::toDomain($model);
    }

    public function findByCinema(CinemaId $cinemaId, ?int $limit = null): array
    {
        $query = EloquentIncident::where('cinema_uuid', $cinemaId->toString())
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get()->map(fn ($model) => IncidentMapper::toDomain($model))->toArray();
    }

    public function findBySalle(SalleId $salleId): array
    {
        $models = EloquentIncident::where('salle_uuid', $salleId->toString())
            ->orderBy('created_at', 'desc')
            ->get();

        return $models->map(fn ($model) => IncidentMapper::toDomain($model))->toArray();
    }

    public function findByEmploye(EmploiId $emploiId): array
    {
        $models = EloquentIncident::where('emploi_declarant_uuid', $emploiId->toString())
            ->orderBy('created_at', 'desc')
            ->get();

        return $models->map(fn ($model) => IncidentMapper::toDomain($model))->toArray();
    }

    public function findByStatut(StatutIncident $statut, ?CinemaId $cinemaId = null): array
    {
        $query = EloquentIncident::where('statut', $statut->value);

        if ($cinemaId) {
            $query->where('cinema_uuid', $cinemaId->toString());
        }

        return $query->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($model) => IncidentMapper::toDomain($model))
            ->toArray();
    }

    public function findBySeverite(SeveriteIncident $severite, ?CinemaId $cinemaId = null): array
    {
        $query = EloquentIncident::where('severite', $severite->value);

        if ($cinemaId) {
            $query->where('cinema_uuid', $cinemaId->toString());
        }

        return $query->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($model) => IncidentMapper::toDomain($model))
            ->toArray();
    }

    public function findByType(TypeIncident $type, ?CinemaId $cinemaId = null): array
    {
        $query = EloquentIncident::where('type_incident', $type->value);

        if ($cinemaId) {
            $query->where('cinema_uuid', $cinemaId->toString());
        }

        return $query->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($model) => IncidentMapper::toDomain($model))
            ->toArray();
    }

    public function findOpenIncidents(?CinemaId $cinemaId = null): array
    {
        $query = EloquentIncident::whereIn('statut', [
            StatutIncident::NOUVEAU->value,
            StatutIncident::EN_COURS->value,
        ]);

        if ($cinemaId) {
            $query->where('cinema_uuid', $cinemaId->toString());
        }

        return $query->orderBy('severite', 'desc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($model) => IncidentMapper::toDomain($model))
            ->toArray();
    }

    public function findCriticalIncidents(?CinemaId $cinemaId = null): array
    {
        $query = EloquentIncident::where('severite', SeveriteIncident::CRITIQUE->value)
            ->whereIn('statut', [
                StatutIncident::NOUVEAU->value,
                StatutIncident::EN_COURS->value,
            ]);

        if ($cinemaId) {
            $query->where('cinema_uuid', $cinemaId->toString());
        }

        return $query->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($model) => IncidentMapper::toDomain($model))
            ->toArray();
    }

    public function findRecentIncidents(int $days = 7, ?CinemaId $cinemaId = null): array
    {
        $query = EloquentIncident::where('created_at', '>=', Carbon::now()->subDays($days));

        if ($cinemaId) {
            $query->where('cinema_uuid', $cinemaId->toString());
        }

        return $query->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($model) => IncidentMapper::toDomain($model))
            ->toArray();
    }

    public function save(Incident $incident): bool
    {
        $model = EloquentIncident::updateOrCreate(
            ['uuid' => $incident->id->toString()],
            IncidentMapper::toPersistence($incident)
        );

        return $model->wasRecentlyCreated || $model->wasChanged();
    }

    public function delete(IncidentId $id): bool
    {
        return EloquentIncident::where('uuid', $id->toString())->delete() > 0;
    }

    public function countByStatut(StatutIncident $statut, ?CinemaId $cinemaId = null): int
    {
        $query = EloquentIncident::where('statut', $statut->value);

        if ($cinemaId) {
            $query->where('cinema_uuid', $cinemaId->toString());
        }

        return $query->count();
    }

    public function getStatistics(CinemaId $cinemaId): array
    {
        $stats = [
            'total'                   => EloquentIncident::where('cinema_uuid', $cinemaId->toString())->count(),
            'by_statut'               => [],
            'by_severite'             => [],
            'by_type'                 => [],
            'open_count'              => 0,
            'critical_count'          => 0,
            'average_resolution_time' => null,
        ];

        // Par statut
        foreach (StatutIncident::cases() as $statut) {
            $stats['by_statut'][$statut->value] = EloquentIncident::where('cinema_uuid', $cinemaId->toString())
                ->where('statut', $statut->value)
                ->count();
        }

        // Par sévérité
        foreach (SeveriteIncident::cases() as $severite) {
            $stats['by_severite'][$severite->value] = EloquentIncident::where('cinema_uuid', $cinemaId->toString())
                ->where('severite', $severite->value)
                ->count();
        }

        // Par type
        foreach (TypeIncident::cases() as $type) {
            $stats['by_type'][$type->value] = EloquentIncident::where('cinema_uuid', $cinemaId->toString())
                ->where('type_incident', $type->value)
                ->count();
        }

        // Incidents ouverts
        $stats['open_count'] = EloquentIncident::where('cinema_uuid', $cinemaId->toString())
            ->whereIn('statut', [StatutIncident::NOUVEAU->value, StatutIncident::EN_COURS->value])
            ->count();

        // Incidents critiques
        $stats['critical_count'] = EloquentIncident::where('cinema_uuid', $cinemaId->toString())
            ->where('severite', SeveriteIncident::CRITIQUE->value)
            ->whereIn('statut', [StatutIncident::NOUVEAU->value, StatutIncident::EN_COURS->value])
            ->count();

        // Temps moyen de résolution
        $resolvedIncidents = EloquentIncident::where('cinema_uuid', $cinemaId->toString())
            ->whereNotNull('date_resolution')
            ->get();

        if ($resolvedIncidents->count() > 0) {
            $totalResolutionTime = $resolvedIncidents->sum(function ($incident) {
                return Carbon::parse($incident->date_resolution)->diffInHours(Carbon::parse($incident->created_at));
            });
            $stats['average_resolution_time'] = round($totalResolutionTime / $resolvedIncidents->count(), 2);
        }

        return $stats;
    }
}
