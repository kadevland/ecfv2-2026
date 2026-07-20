<?php

declare(strict_types=1);

namespace App\Application\Film\Queries\GetFilmDetail;

use Exception;
use App\Domain\Cinema\Entities\Film;
use App\Application\Contracts\Result;
use App\Domain\Cinema\ValueObjects\FilmId;
use App\Application\Film\DTOs\FilmDetailDto;
use App\Application\Contracts\QueryInterface;
use App\Application\Contracts\QueryHandlerInterface;
use App\Domain\Cinema\Repositories\FilmRepositoryInterface;
use App\Domain\Cinema\Repositories\SeanceRepositoryInterface;

final class GetFilmDetailQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly FilmRepositoryInterface $filmRepository,
        private readonly SeanceRepositoryInterface $seanceRepository
    ) {}

    public function handle(QueryInterface $query): Result
    {
        if (!$query instanceof GetFilmDetailQuery) {
            return Result::error(
                'INVALID_QUERY_TYPE',
                'Type de requête invalide'
            );
        }

        try {
            if (!$query->isValid()) {
                return Result::error(
                    'INVALID_QUERY',
                    'UUID du film requis'
                );
            }

            $filmId = FilmId::fromString($query->filmUuid);

            // Utiliser le repository au lieu du modèle directement
            $film = $this->filmRepository->findById($filmId);

            if (!$film) {
                return Result::error(
                    'FILM_NOT_FOUND',
                    'Film non trouvé'
                );
            }

            $dto = $this->mapToDetailDto($film, $query);

            return Result::success($dto);

        } catch (Exception $e) {
            return Result::error(
                'QUERY_FAILED',
                'Erreur lors de la récupération: ' . $e->getMessage()
            );
        }
    }

    private function mapToDetailDto(Film $film, GetFilmDetailQuery $query): FilmDetailDto
    {
        // Charger les séances si demandées
        $seancesAVenir = [];
        if ($query->includeSeances) {
            $seancesEntites = $this->seanceRepository->findByFilmId($film->id);
            // Filtrer les séances à venir et limiter à 10
            $seancesEntites = array_filter($seancesEntites, fn ($seance) => $seance->isUpcoming());
            $seancesEntites = array_slice($seancesEntites, 0, 10);

            $seancesAVenir = array_map(fn ($seance) => [
                'uuid'      => $seance->id->value,
                'dateHeure' => $seance->dateHeureDebut->format('Y-m-d H:i:s'),
                'version'   => $seance->version,
                'prixMin'   => ($prix = $seance->getTarification()->getPrixMinimum()) ? $prix->getAmount() / 100 : 0,
                'salleNom'  => 'Salle ' . ($seance->salleId->value ?? 'N/A'),
            ], $seancesEntites);
        }

        $avisRecents = [];

        return new FilmDetailDto(
            uuid: $film->id->value,
            titre: $film->titre,
            titreFr: $film->titreOriginal,
            realisateurs: $film->realisateurs,
            realisateurPrincipal: $film->getPrimaryDirector(),
            acteursPrincipaux: $film->acteursPrincipaux,
            genres: $film->genres,
            dureeMinutes: $film->dureeMinutes,
            dureeFormatted: $film->getFormattedDuration(),
            classification: $film->classification,
            langueOriginale: $film->langueOriginale ?? 'Inconnu',
            sousTitres: $film->sousTitres ?? [],
            resume: $film->synopsis,
            dateSortie: $film->dateSortie->format('Y-m-d'),
            dateFinExploitation: $film->dateFinExploitation?->format('Y-m-d'),
            notePresse: $film->noteCritique,
            notePublic: $film->notePublic,
            noteMoyenneAvis: $film->noteMoyenneAvis,
            nombreAvis: $film->nombreAvis,
            afficheUrl: $film->afficheUrl,
            bandeAnnonceUrl: $film->bandeAnnonceUrl,
            estActif: $film->estActif,
            isInTheaters: $film->isInTheaters(),
            seancesAVenir: $seancesAVenir,
            avisRecents: $avisRecents,
        );
    }
}
