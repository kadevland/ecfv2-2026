<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Domain\Enums\GenreFilm;
use App\Http\Controllers\Controller;
use App\Domain\Enums\ClassificationFilm;
use App\Domain\Public\Repositories\FilmRepositoryInterface;
use App\Domain\Public\Repositories\SeanceRepositoryInterface;

final class FilmCatalogueController extends Controller
{
    public function __construct (
        protected FilmRepositoryInterface $filmRepository,
        protected SeanceRepositoryInterface $seanceRepository,
    ) {}
    public function index (Request $request) : View
    {
        // Utiliser le repository pour les films avec filtres
        $films = $this->filmRepository->getFilmsCatalogueIndex(
            search: $request->get('search'),
            genre: $request->get('genre'),
            classification: $request->get('classification'),
            perPage: 12
        );


        // Transformer les films pour la vue
        $films->getCollection()
            ->transform(function ($film) {
                return $this->mapFilmForView($film);
            });

        // Get filter options from enums
        $genres = collect(GenreFilm::cases())->mapWithKeys(function ($genre) {
            return [$genre->value => $genre->label()];
        })
            ->toArray();

        $classifications = collect(ClassificationFilm::cases())->mapWithKeys(function ($classification) {
            return [$classification->value => $classification->label()];
        })
            ->toArray();

        return view('public.films.index', [
            'films'           => $films,
            'genres'          => $genres,
            'classifications' => $classifications,
            'filters'         => $request->only(['genre', 'classification']),
            'currentSort'     => 'recent',
        ]);
    }

    public function byGenre (Request $request, string $genre) : View
    {
        // Utiliser le repository pour les films par genre
        $films = $this->filmRepository->getFilmsByGenrePaginated($genre, 12);

        // Transformer les films pour la vue
        $films->getCollection()
            ->transform(function ($film) {
                return $this->mapFilmForView($film);
            });

        return view('public.films.by-genre', [
            'films'        => $films,
            'genre'        => $genre,
            'genreDisplay' => ucfirst($genre),
        ]);
    }

    public function show (Request $request, string $id) : View
    {
        // Temporary fallback to MongoDB direct access until CQRS is fixed
        $filmData = $this->filmRepository->findByFilmId($id);

        if (!$filmData) {
            abort(404, 'Film non trouvé');
        }

        $film = $this->mapFilmForView($filmData);

        $seancesData = $this->seanceRepository->getSeanceUpcomingForFilm($id);

        // Transformer les séances d'abord
        $seancesTransformed = $seancesData->map(function ($seance) {
            return (object) [
                'seance_id'          => $seance->seance_id,
                'cinema_id'          => $seance->cinema_id,
                'nom_cinema'         => $seance->cinema_nom,
                'nom_salle'          => $seance->salle_nom,
                'date_heure_debut'   => \Carbon\Carbon::parse($seance->date_heure_debut),
                'date_heure_fin'     => \Carbon\Carbon::parse($seance->date_heure_fin),
                'places_disponibles' => $seance->places_disponibles,
                'version'            => $seance->version ?? 'VF',
                'qualite_projection' => 'Standard',
            ];
        });

        // Puis grouper par date
        $seances = $seancesTransformed->groupBy(function ($seance) {
            return $seance->date_heure_debut->format('Y-m-d');
        });

        $cinemas = $this->seanceRepository->getSeanceCinemaForFilm($id);


        return view('public.films.show', [
            'film'    => $film,
            'seances' => $seances,
            'cinemas' => $cinemas,
        ]);
    }

    public function seances (Request $request, string $filmId) : View
    {
        $filmData = $this->filmRepository->findByFilmId($filmId);

        if (!$filmData) {
            abort(404, 'Film non trouvé');
        }

        $film = $this->mapFilmForView($filmData);

        $seancesData = $this->seanceRepository->getSeanceForFilm($filmId, $request->input('cinema', null), $request->input('date', null));

        //dd($this->seanceRepository);

        // Transformer les séances avec mappage de propriétés
        $seancesTransformed = $seancesData->map(function ($seance) {


            $tarifMin = min($seance->tarifs['tarifs_base'] ?? ['normal' => 1250]) * .01;


            return (object) [
                'seance_id'          => $seance->seance_id,
                'cinema_id'          => (string) $seance->cinema_id,  // Forcer string pour consistance
                'nom_cinema'         => $seance->cinema_nom,
                'nom_salle'          => $seance->salle_nom,
                'date_heure_debut'   => \Carbon\Carbon::parse($seance->date_heure_debut),
                'date_heure_fin'     => \Carbon\Carbon::parse($seance->date_heure_fin),
                'places_disponibles' => $seance->places_disponibles,
                'version'            => $seance->version ?? 'VF',
                'qualite_projection' => $seance->qualite_projection ?? 'Standard',
                'tarifMin'           => $tarifMin,
            ];
        });

        // Grouper AVANT la pagination pour éviter les incohérences
        $seancesByDateAndCinema = $seancesTransformed
            ->groupBy(function ($seance) {
                return $seance->date_heure_debut->format('Y-m-d');
            })
            ->map(function ($seancesOfDay) {
                return $seancesOfDay->groupBy('cinema_id');
            });

        // Pagination manuelle APRÈS groupement
        $perPage     = 5;
        $currentPage = $request->get('page', 1);

        // Calculer le total de séances pour la pagination
        $totalSeances = $seancesByDateAndCinema->count();

        // Paginer les données groupées (prendre seulement les X premières dates)
        $seancesPaginated = $seancesByDateAndCinema->slice(($currentPage - 1) * $perPage, $perPage); // 5 dates par page

        //dd($seancesPaginated);

        $paginatedSeances = new \Illuminate\Pagination\LengthAwarePaginator(
            $seancesPaginated,
            $totalSeances,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $seances = $paginatedSeances;

        // Récupérer TOUS les cinémas disponibles pour ce film (avec séances futures uniquement)
        $allCinemasData = $this->seanceRepository->getSeanceCinemaForFilm($filmId);

        $cinemasDisponibles = $allCinemasData
            ->unique('id')
            ->pluck('nom', 'id')
            ->filter() // Supprimer les valeurs vides
            ->sort(); // Trier par nom

        return view('public.films.seances', [
            'film'                     => $film,
            'seances'                  => $seances,
            'paginatedSeances'         => $paginatedSeances,
            'cinemasDisponibles'       => $cinemasDisponibles,
            'filters'                  => $request->only(['cinema', 'date']),
            'totalSeances'              => $seancesData->count(),
            //'totalSeancesAfterFilter'  => $seancesTransformed->count(),
        ]);
    }

    public function ratings (Request $request, string $filmId) : View
    {
        // Utiliser le repository pour récupérer le film
        $filmData = $this->filmRepository->findByFilmId($filmId);

        if (!$filmData) {
            abort(404, 'Film non trouvé');
        }

        $film = $this->mapFilmForView($filmData);

        // Utiliser le repository pour les avis approuvés
        $avisData = $this->filmRepository->getApprovedReviews($filmId);

        $avis = $avisData->map(function ($avis) {
            return (object) $avis;
        });

        // Calculer les statistiques des notes
        $statistiques = [
            'total_avis'   => $avis->count(),
            'note_moyenne' => $avis->avg('note') ?? 0,
            'repartition'  => [
                5 => $avis->where('note', 5)
                    ->count(),
                4 => $avis->where('note', 4)
                    ->count(),
                3 => $avis->where('note', 3)
                    ->count(),
                2 => $avis->where('note', 2)
                    ->count(),
                1 => $avis->where('note', 1)
                    ->count(),
            ],
        ];

        return view('public.films.ratings', [
            'film'         => $film,
            'avis'         => $avis,
            'statistiques' => $statistiques,
        ]);
    }

    public function storeRating (Request $request, string $filmId) : \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'note'            => 'required|integer|min:1|max:5',
            'commentaire'     => 'nullable|string|max:1000',
            'nom_utilisateur' => 'required|string|max:100',
            'email'           => 'required|email|max:255',
        ]);

        // Utiliser le repository pour vérifier que le film existe
        $filmExists = $this->filmRepository->filmExists($filmId);

        if (!$filmExists) {
            abort(404, 'Film non trouvé');
        }

        // Utiliser le repository pour insérer l'avis
        $this->filmRepository->insertReview([
            'film_id'           => $filmId,
            'note'              => $validated['note'],
            'commentaire'       => $validated['commentaire'] ?? '',
            'nom_utilisateur'   => $validated['nom_utilisateur'],
            'email'             => $validated['email'],
            'statut'            => 'en_attente', // Les avis doivent être modérés
            'date_creation'     => now(),
            'date_modification' => now(),
        ]);

        return redirect()
            ->route('films.ratings', $filmId)
            ->with('success', 'Votre avis a été soumis avec succès ! Il sera publié après modération.');
    }

    /**
     * @param mixed $filmData
     */
    private function mapFilmForView ($filmData) : object
    {
        $filmArray = (array) $filmData;

        // Note: MongoDB collection already uses correct field names:
        // - film_id, titre, genre, duree, classification, description, realisateur, etc.

        // Map note properties - MongoDB uses 'note_moyenne', not 'note_moyenne_avis'
        // No mapping needed, field is already correct

        // Genre property - MongoDB uses 'genre' as string, no mapping needed
        // No mapping needed, field is already correct

        // Duration property - MongoDB uses 'duree' as integer, no mapping needed
        // No mapping needed, field is already correct

        // Description property - MongoDB uses 'description', no mapping needed
        // No mapping needed, field is already correct

        // Director property - MongoDB uses 'realisateur' as string, no mapping needed
        // No mapping needed, field is already correct

        // Actors property - MongoDB uses 'acteurs_principaux', may need parsing if string
        if (isset($filmArray['acteurs_principaux'])) {
            // If it's a string with comma-separated actors, convert to array
            $acteurs = is_array($filmArray['acteurs_principaux']) ? $filmArray['acteurs_principaux'] : json_decode($filmArray['acteurs_principaux']);

            if (is_array($acteurs)) {
                $filmArray['acteurs_principaux'] = $acteurs;
            }

            // $filmArray['acteurs_principaux'] = array_map('trim', explode(',', $filmArray['acteurs_principaux']));
        }

        // Cast date fields to Carbon instances if needed
        if (isset($filmArray['date_sortie']) && is_string($filmArray['date_sortie'])) {
            $filmArray['date_sortie'] = \Carbon\Carbon::parse($filmArray['date_sortie']);
        }

        // Handle legacy genres (array) to genre (string) if needed
        if (empty($filmArray['genre']) && !empty($filmArray['genres'])) {
            $genresArray = is_string($filmArray['genres'])
                ? json_decode($filmArray['genres'], true)
                : $filmArray['genres'];

            if (is_array($genresArray) && !empty($genresArray)) {
                $filmArray['genre'] = $genresArray[0];
            }
        }

        // Handle legacy duree_minutes to duree if needed
        if (empty($filmArray['duree']) && !empty($filmArray['duree_minutes'])) {
            $filmArray['duree'] = $filmArray['duree_minutes'];
        }

        // Handle legacy realisateurs (array) to realisateur (string) if needed
        if (empty($filmArray['realisateur']) && !empty($filmArray['realisateurs'])) {
            $realisateursArray = is_string($filmArray['realisateurs'])
                ? json_decode($filmArray['realisateurs'], true)
                : $filmArray['realisateurs'];

            if (is_array($realisateursArray) && !empty($realisateursArray)) {
                $filmArray['realisateur'] = is_string($realisateursArray[0])
                    ? $realisateursArray[0]
                    : ($realisateursArray[0]['nom'] ?? 'Inconnu');
            }
        }

        // Handle legacy note_moyenne_avis to note_moyenne if needed
        if (($filmArray['note_moyenne'] ?? 0) === 0 && !empty($filmArray['note_moyenne_avis'])) {
            $filmArray['note_moyenne'] = $filmArray['note_moyenne_avis'];
        }

        // Handle legacy synopsis to description if needed
        if (empty($filmArray['description']) && !empty($filmArray['synopsis'])) {
            $filmArray['description'] = $filmArray['synopsis'];
        }

        // Force notes to 0 to hide rating info from public listing
        $filmArray['note_moyenne']   = 0;
        $filmArray['nombre_avis']    = 0;
        $filmArray['duree']          = $filmArray['duree'] ?? 0;
        $filmArray['genre']          = $filmArray['genre'] ?? 'Non spécifié';
        $filmArray['description']    = $filmArray['description'] ?? '';
        $filmArray['realisateur']    = $filmArray['realisateur'] ?? '';
        $filmArray['affiche_url']    = $filmArray['affiche_url'] ?? '';
        $filmArray['classification'] = $filmArray['classification'] ?? '';

        return (object) $filmArray;
    }
}
