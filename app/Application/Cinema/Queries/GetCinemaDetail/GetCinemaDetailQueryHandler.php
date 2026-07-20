<?php

declare(strict_types=1);

namespace App\Application\Cinema\Queries\GetCinemaDetail;

use Exception;
use App\Application\Contracts\Result;
use App\Domain\Cinema\Entities\Cinema;
use App\Domain\Cinema\ValueObjects\CinemaId;
use App\Application\Contracts\QueryInterface;
use App\Application\Cinema\DTOs\CinemaDetailDto;
use App\Application\Contracts\QueryHandlerInterface;
use App\Domain\Cinema\Repositories\SalleRepositoryInterface;
use App\Domain\Cinema\Repositories\CinemaRepositoryInterface;
use App\Domain\Cinema\Repositories\SeanceRepositoryInterface;

final class GetCinemaDetailQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly CinemaRepositoryInterface $cinemaRepository,
        private readonly SalleRepositoryInterface $salleRepository,
        private readonly SeanceRepositoryInterface $seanceRepository
    ) {}

    public function handle(QueryInterface $query): Result
    {
        assert($query instanceof GetCinemaDetailQuery);

        try {
            $cinemaId = new CinemaId($query->cinemaUuid);

            // Utiliser le repository au lieu du modèle directement
            $cinema = $this->cinemaRepository->findById($cinemaId);

            if (!$cinema) {
                return Result::error(
                    'CINEMA_NOT_FOUND',
                    'Cinéma non trouvé'
                );
            }

            $dto = $this->mapToDetailDto($cinema, $query);

            return Result::success($dto);

        } catch (Exception $e) {

            return Result::error(
                'QUERY_FAILED',
                'Erreur lors de la récupération: ' . $e->getMessage()
            );
        }
    }

    private function mapToDetailDto(Cinema $cinema, GetCinemaDetailQuery $query): CinemaDetailDto
    {
        // Compter les salles du cinéma
        $nombreSalles = $this->salleRepository->countByCinema($cinema->id);

        // Charger les salles si demandées
        $salles        = [];
        $sallesEntites = null;
        if ($query->includeSalles) {
            $sallesEntites = $this->salleRepository->findByCinemaId($cinema->id);
            $salles        = array_map(fn ($salle) => [
                'uuid' => $salle->id->value,
                'nom'  => $salle->nom,
                // @phpstan-ignore-next-line - Property not yet implemented in domain model
                'numero'         => $salle->numero ?? 1,
                'capaciteTotale' => $salle->capaciteTotale,
                // @phpstan-ignore-next-line - Property not yet implemented in domain model
                'technologies'     => $salle->technologies ?? [],
                'accessibilitePmr' => $salle->accessibilitePmr,
                'statut'           => $salle->statut,
            ], $sallesEntites);
        }

        // Charger les séances à venir si demandées
        $seancesAVenir = [];
        if ($query->includeSeances) {
            $seancesEntites = $this->seanceRepository->findByCinemaId($cinema->id);
            // Filtrer les séances à venir et limiter à 10
            $seancesEntites = array_filter($seancesEntites, fn ($seance) => $seance->isUpcoming());
            $seancesEntites = array_slice($seancesEntites, 0, 10);

            $seancesAVenir = array_map(fn ($seance) => [
                'uuid'      => $seance->id->value,
                'dateHeure' => $seance->dateHeureDebut->format('Y-m-d H:i:s'),
                'version'   => $seance->version,
                'prixMin'   => ($prix = $seance->getTarification()->getPrixMinimum()) ? $prix->getAmount() / 100 : 0,
            ], $seancesEntites);
        }

        // Calculer l'accessibilité PMR du cinéma
        $accessibilitePmr = $this->calculateAccessibilitePmr($cinema->id, $sallesEntites);

        return new CinemaDetailDto(
            uuid: $cinema->id->value,
            nom: $cinema->nom,
            pays: $cinema->pays->value,
            adresse: $cinema->adresse->rue,
            ville: $cinema->adresse->ville,
            codePostal: $cinema->adresse->codePostal,
            telephone: $cinema->telephone?->telephoneInternational,
            email: $cinema->email?->value,
            description: $cinema->description,
            estActif: $cinema->estActif,
            latitude: $cinema->coordonneesGps?->latitude,
            longitude: $cinema->coordonneesGps?->longitude,
            nombreSalles: $nombreSalles,
            horairesOuverture: $cinema->horairesOuverture,
            accessibilitePmr: $accessibilitePmr,
            salles: $salles,
            seancesAVenir: $seancesAVenir,
            createdAt: null,
            updatedAt: null,
        );
    }

    /**
     * Calcule l'accessibilité PMR du cinéma de manière intelligente
     *
     * @param array<mixed>|null $sallesDejaChargees
     */
    private function calculateAccessibilitePmr(CinemaId $cinemaId, ?array $sallesDejaChargees): bool
    {
        // Si on a déjà chargé les salles (includeSalles=true), les utiliser directement (zéro requête)
        if ($sallesDejaChargees !== null) {
            // Vérifier s'il y a au moins une salle PMR active
            foreach ($sallesDejaChargees as $salle) {
                if ($salle->accessibilitePmr && $salle->statut === 'active') {
                    return true;
                }
            }

            return false;
        }

        // Si pas de salles chargées, utiliser la requête optimisée hasAccessibleRoomByCinema()
        // Cette méthode fait query()->count() > 0 au lieu de charger toutes les entités
        return $this->salleRepository->hasAccessibleRoomByCinema($cinemaId);
    }
}
