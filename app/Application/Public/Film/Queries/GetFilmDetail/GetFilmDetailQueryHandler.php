<?php

declare(strict_types=1);

namespace App\Application\Public\Film\Queries\GetFilmDetail;

use Exception;
use Illuminate\Support\Carbon;
use App\Application\Contracts\Result;
use App\Application\Contracts\QueryInterface;
use App\Application\Contracts\QueryHandlerInterface;
use App\Infrastructure\Database\ReadModels\FilmCatalogue;
use App\Infrastructure\Database\Models\MongoDB\SeancePublic;

/**
 * Handler MongoDB pour récupérer les détails d'un film public
 */
final class GetFilmDetailQueryHandler implements QueryHandlerInterface
{
    public function handle(QueryInterface $query): Result
    {
        assert($query instanceof GetFilmDetailQuery);

        try {
            // Récupérer le film
            $film = FilmCatalogue::where('film_id', $query->filmId)
                ->actif()
                ->first();

            if (!$film) {
                return Result::error(
                    'FILM_NOT_FOUND',
                    'Film introuvable ou inactif'
                );
            }

            // Récupérer les séances futures groupées par date
            /** @var array<string, array<string, mixed>> $seances */
            $seances = SeancePublic::where('film_id', $query->filmId)
                ->future()
                ->disponible()
                ->orderBy('date_heure_debut')
                ->get()
                ->groupBy(function (SeancePublic $seance) {
                    // MongoDB dates are cast to Carbon via the model
                    return $seance->date_heure_debut->format('Y-m-d');
                })
                ->map(function ($seancesOfDay) {
                    return $seancesOfDay->map(function (SeancePublic $seance) {
                        return [
                            'seance_id'          => $seance->seance_id,
                            'cinema_id'          => $seance->cinema_id,
                            'nom_cinema'         => $seance->cinema_nom,
                            'nom_salle'          => $seance->salle_nom,
                            'date_heure_debut'   => $seance->date_heure_debut->toISOString(),
                            'date_heure_fin'     => $seance->date_heure_fin->toISOString(),
                            'places_disponibles' => $seance->places_disponibles,
                            'version'            => $seance->version ?? 'VF',
                            'qualite_projection' => 'Standard',
                        ];
                    })->toArray();
                })
                ->toArray();

            // Récupérer les cinémas qui diffusent le film
            /** @var array<int, array<string, string>> $cinemas */
            $cinemas = SeancePublic::where('film_id', $query->filmId)
                ->future()
                ->select(['cinema_id', 'cinema_nom'])
                ->distinct()
                ->get()
                ->map(function (SeancePublic $cinema) {
                    return [
                        'id'      => $cinema->cinema_id,
                        'nom'     => $cinema->cinema_nom,
                        'adresse' => 'Voir détails cinéma',
                    ];
                })
                ->toArray();

            // Formater les données du film
            /** @var array<string, mixed> $filmData */
            $filmData = [
                'film_id'            => $film->film_id,
                'titre'              => $film->titre,
                'titre_original'     => $film->titre_original,
                'realisateurs'       => $film->realisateurs ?? [],
                'acteurs_principaux' => $film->acteurs_principaux ?? [],
                'genres'             => $film->genres ?? [],
                'duree_minutes'      => $film->duree_minutes,
                'duree_formatee'     => $film->getFormattedDuration(),
                'classification'     => $film->classification,
                'date_sortie'        => $film->date_sortie->format('Y-m-d'),
                'note_critique'      => $film->note_critique,
                'note_public'        => $film->note_public,
                'note_moyenne_avis'  => $film->note_moyenne_avis,
                'nombre_avis'        => $film->nombre_avis,
                'affiche_url'        => $film->affiche_url,
                'synopsis'           => $film->synopsis,
                'statut'             => $film->statut,
                'est_actif'          => $film->est_actif,
            ];

            $response = new GetFilmDetailQueryResponse(
                film: $filmData,
                seances: $seances,
                cinemas: $cinemas
            );

            return Result::success($response);

        } catch (Exception $e) {
            return Result::error(
                'FILM_DETAIL_QUERY_FAILED',
                'Erreur lors de la récupération des détails du film: ' . $e->getMessage()
            );
        }
    }
}
