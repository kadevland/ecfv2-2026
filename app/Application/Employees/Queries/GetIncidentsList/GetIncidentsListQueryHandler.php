<?php

declare(strict_types=1);

namespace App\Application\Employees\Queries\GetIncidentsList;

use Exception;
use App\Application\Contracts\Result;
use App\Domain\Cinema\ValueObjects\SalleId;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Application\Contracts\QueryInterface;
use App\Domain\Employees\ValueObjects\EmploiId;
use App\Application\Contracts\QueryHandlerInterface;
use App\Domain\Employees\Repositories\IncidentRepositoryInterface;

final class GetIncidentsListQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly IncidentRepositoryInterface $incidentRepository,
    ) {}

    public function handle(QueryInterface $query): Result
    {
        assert($query instanceof GetIncidentsListQuery);

        try {
            $incidents = [];

            // Filtrer par cinéma
            if ($query->cinemaUuid !== null) {
                $cinemaId = CinemaId::fromString($query->cinemaUuid);

                if ($query->criticalOnly === true) {
                    $incidents = $this->incidentRepository->findCriticalIncidents($cinemaId);
                } elseif ($query->openOnly === true) {
                    $incidents = $this->incidentRepository->findOpenIncidents($cinemaId);
                } elseif ($query->recentDays !== null) {
                    $incidents = $this->incidentRepository->findRecentIncidents($query->recentDays, $cinemaId);
                } elseif ($query->statut !== null) {
                    $incidents = $this->incidentRepository->findByStatut($query->statut, $cinemaId);
                } elseif ($query->severite !== null) {
                    $incidents = $this->incidentRepository->findBySeverite($query->severite, $cinemaId);
                } elseif ($query->type !== null) {
                    $incidents = $this->incidentRepository->findByType($query->type, $cinemaId);
                } else {
                    $incidents = $this->incidentRepository->findByCinema($cinemaId, $query->limit);
                }
            }
            // Filtrer par employé
            elseif ($query->emploiUuid !== null) {
                $emploiId  = EmploiId::fromString($query->emploiUuid);
                $incidents = $this->incidentRepository->findByEmploye($emploiId);
            }
            // Filtrer par salle
            elseif ($query->salleUuid !== null) {
                $salleId   = SalleId::fromString($query->salleUuid);
                $incidents = $this->incidentRepository->findBySalle($salleId);
            }
            // Cas spéciaux sans cinéma
            elseif ($query->criticalOnly === true) {
                $incidents = $this->incidentRepository->findCriticalIncidents();
            } elseif ($query->openOnly === true) {
                $incidents = $this->incidentRepository->findOpenIncidents();
            } elseif ($query->recentDays !== null) {
                $incidents = $this->incidentRepository->findRecentIncidents($query->recentDays);
            } elseif ($query->statut !== null) {
                $incidents = $this->incidentRepository->findByStatut($query->statut);
            } elseif ($query->severite !== null) {
                $incidents = $this->incidentRepository->findBySeverite($query->severite);
            } elseif ($query->type !== null) {
                $incidents = $this->incidentRepository->findByType($query->type);
            }

            // Appliquer la limite si spécifiée et pas déjà appliquée
            if ($query->limit !== null && count($incidents) > $query->limit) {
                $incidents = array_slice($incidents, 0, $query->limit);
            }

            // Tri personnalisé si nécessaire
            if ($query->sortBy !== 'created_at' || $query->sortDirection !== 'desc') {
                usort($incidents, function ($a, $b) use ($query) {
                    $aValue = $this->getIncidentValue($a, $query->sortBy);
                    $bValue = $this->getIncidentValue($b, $query->sortBy);

                    $comparison = $aValue <=> $bValue;

                    return $query->sortDirection === 'asc' ? $comparison : -$comparison;
                });
            }

            // Préparer les statistiques si demandé pour un cinéma
            $statistics = null;
            if ($query->cinemaUuid !== null) {
                $cinemaId   = CinemaId::fromString($query->cinemaUuid);
                $statistics = $this->incidentRepository->getStatistics($cinemaId);
            }

            return Result::success([
                'incidents'  => $incidents,
                'total'      => count($incidents),
                'statistics' => $statistics,
            ]);

        } catch (Exception $e) {
            return Result::error(
                'QUERY_FAILED',
                'Erreur lors de la récupération des incidents: ' . $e->getMessage()
            );
        }
    }

    private function getIncidentValue(object $incident, string $field): mixed
    {
        return match ($field) {
            'titre'           => $incident->titre,
            'severite'        => $incident->severite,
            'statut'          => $incident->statut,
            'type_incident'   => $incident->typeIncident,
            'date_resolution' => $incident->dateResolution?->format('Y-m-d H:i:s'),
            default           => $incident->id->value,
        };
    }
}
