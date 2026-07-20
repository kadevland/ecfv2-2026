<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\ReadModels;

use DateTime;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Infrastructure\Database\Schemas\Cinema\FilmCatalogueSchema;

/**
 * Modèle MongoDB pour la collection films_catalogue
 * Collection optimisée pour l'affichage du catalogue public
 *
 * @property string $titre
 * @property string $description
 * @property string $genre
 * @property int $duree
 * @property string $classification
 * @property float $note_moyenne
 * @property int $nombre_avis
 * @property string $film_id
 * @property string $date_sortie
 * @property string $realisateur
 * @property string $acteurs_principaux
 * @property string $affiche_url
 * @property string $bande_annonce_url
 * @property string $statut_diffusion
 * @property string $statut
 */
class FilmCatalogue extends Model
{
    use SoftDeletes;

    public $connection = FilmCatalogueSchema::CONNECTION;

    protected $collection = FilmCatalogueSchema::COLLECTION;

    protected $primaryKey = '_id';

    protected $fillable = [
        '_id', // MongoDB primary key
        FilmCatalogueSchema::FILM_ID,
        FilmCatalogueSchema::TITRE,
        'titre_original',
        FilmCatalogueSchema::DESCRIPTION,
        'synopsis',
        FilmCatalogueSchema::GENRE,
        'genres',
        FilmCatalogueSchema::DUREE,
        'duree_minutes',
        FilmCatalogueSchema::CLASSIFICATION,
        'langue_originale',
        'pays_origine',
        FilmCatalogueSchema::DATE_SORTIE,
        'date_fin_exploitation',
        FilmCatalogueSchema::REALISATEUR,
        'realisateurs',
        FilmCatalogueSchema::ACTEURS_PRINCIPAUX,
        'sous_titres',
        'producteur',
        'images_additionnelles',
        'metadonnees_techniques',
        FilmCatalogueSchema::AFFICHE_URL,
        FilmCatalogueSchema::BANDE_ANNONCE_URL,
        'note_critique',
        'note_public',
        FilmCatalogueSchema::NOTE_MOYENNE,
        'note_moyenne_avis',
        FilmCatalogueSchema::NOMBRE_AVIS,
        FilmCatalogueSchema::STATUT_DIFFUSION,
        FilmCatalogueSchema::STATUT,
        'est_actif',
        'created_at', // Timestamps
        'updated_at',
    ];

    /** @var array<string> */
    protected $dates = [
        FilmCatalogueSchema::DATE_SORTIE,
        FilmCatalogueSchema::CREATED_AT,
        FilmCatalogueSchema::UPDATED_AT,
        FilmCatalogueSchema::DELETED_AT,
    ];

    /**
     * Agrégation MongoDB pour obtenir les statistiques par genre
     */
    public static function getGenreStats(): mixed
    {
        return static::raw(function ($collection) {
            return $collection->aggregate([
                [
                    '$match' => [
                        FilmCatalogueSchema::STATUT_DIFFUSION => 'en_diffusion',
                    ],
                ],
                [
                    '$group' => [
                        '_id'        => '$' . FilmCatalogueSchema::GENRE,
                        'count'      => ['$sum' => 1],
                        'avg_note'   => ['$avg' => '$' . FilmCatalogueSchema::NOTE_MOYENNE],
                        'total_avis' => ['$sum' => '$' . FilmCatalogueSchema::NOMBRE_AVIS],
                    ],
                ],
                [
                    '$sort' => ['count' => -1],
                ],
            ]);
        });
    }

    /**
     * Agrégation MongoDB pour obtenir les films populaires par cinéma
     */
    public static function getPopularFilmsByCinema(string $cinemaId, int $limit = 10): mixed
    {
        return static::raw(function ($collection) use ($cinemaId, $limit) {
            return $collection->aggregate([
                [
                    '$match' => [
                        FilmCatalogueSchema::STATUT_DIFFUSION                 => 'en_diffusion',
                        FilmCatalogueSchema::CINEMAS_DIFFUSION . '.cinema_id' => $cinemaId,
                    ],
                ],
                [
                    '$addFields' => [
                        'popularity_score' => [
                            '$multiply' => [
                                '$' . FilmCatalogueSchema::NOTE_MOYENNE,
                                '$' . FilmCatalogueSchema::NOMBRE_AVIS,
                            ],
                        ],
                    ],
                ],
                [
                    '$sort' => ['popularity_score' => -1],
                ],
                [
                    '$limit' => $limit,
                ],
                [
                    '$project' => [
                        FilmCatalogueSchema::FILM_ID      => 1,
                        FilmCatalogueSchema::TITRE        => 1,
                        FilmCatalogueSchema::GENRE        => 1,
                        FilmCatalogueSchema::NOTE_MOYENNE => 1,
                        FilmCatalogueSchema::NOMBRE_AVIS  => 1,
                        'popularity_score'                => 1,
                    ],
                ],
            ]);
        });
    }

    /**
     * Recherche full-text avec score de pertinence
     */
    public static function searchWithRelevance(string $searchTerm, int $limit = 20): mixed
    {
        return static::raw(function ($collection) use ($searchTerm, $limit) {
            $regex = new \MongoDB\BSON\Regex($searchTerm, 'i');

            return $collection->aggregate([
                [
                    '$match' => [
                        '$or' => [
                            [FilmCatalogueSchema::TITRE => ['$regex' => $regex]],
                            [FilmCatalogueSchema::DESCRIPTION        => ['$regex' => $regex]],
                            [FilmCatalogueSchema::REALISATEUR        => ['$regex' => $regex]],
                            [FilmCatalogueSchema::ACTEURS_PRINCIPAUX => ['$regex' => $regex]],
                        ],
                    ],
                ],
                [
                    '$addFields' => [
                        'relevance_score' => [
                            '$add' => [
                                ['$cond' => [['$regexMatch' => ['input' => '$' . FilmCatalogueSchema::TITRE, 'regex' => $regex]], 10, 0]],
                                ['$cond' => [['$regexMatch' => ['input' => '$' . FilmCatalogueSchema::DESCRIPTION, 'regex' => $regex]], 5, 0]],
                                ['$cond' => [['$regexMatch' => ['input' => '$' . FilmCatalogueSchema::REALISATEUR, 'regex' => $regex]], 3, 0]],
                                ['$cond' => [['$regexMatch' => ['input' => '$' . FilmCatalogueSchema::ACTEURS_PRINCIPAUX, 'regex' => $regex]], 2, 0]],
                            ],
                        ],
                    ],
                ],
                [
                    '$sort' => [
                        'relevance_score'                 => -1,
                        FilmCatalogueSchema::NOTE_MOYENNE => -1,
                    ],
                ],
                [
                    '$limit' => $limit,
                ],
            ]);
        });
    }

    /**
     * Obtient les films avec le nombre de séances par jour
     */
    public static function getFilmsWithDailySeances(string $dateStart, string $dateEnd): mixed
    {
        return static::raw(function ($collection) use ($dateStart, $dateEnd) {
            return $collection->aggregate([
                [
                    '$match' => [
                        FilmCatalogueSchema::STATUT_DIFFUSION => 'en_diffusion',
                    ],
                ],
                [
                    '$unwind' => '$' . FilmCatalogueSchema::PROCHAINES_SEANCES,
                ],
                [
                    '$match' => [
                        FilmCatalogueSchema::PROCHAINES_SEANCES . '.date_heure_debut' => [
                            '$gte' => new \MongoDB\BSON\UTCDateTime(strtotime($dateStart) * 1000),
                            '$lte' => new \MongoDB\BSON\UTCDateTime(strtotime($dateEnd) * 1000),
                        ],
                    ],
                ],
                [
                    '$group' => [
                        '_id' => [
                            'film_id' => '$' . FilmCatalogueSchema::FILM_ID,
                            'date'    => [
                                '$dateToString' => [
                                    'format' => '%Y-%m-%d',
                                    'date'   => '$' . FilmCatalogueSchema::PROCHAINES_SEANCES . '.date_heure_debut',
                                ],
                            ],
                        ],
                        'titre'         => ['$first' => '$' . FilmCatalogueSchema::TITRE],
                        'genre'         => ['$first' => '$' . FilmCatalogueSchema::GENRE],
                        'seances_count' => ['$sum' => 1],
                        'cinemas'       => ['$addToSet' => '$' . FilmCatalogueSchema::PROCHAINES_SEANCES . '.cinema_id'],
                    ],
                ],
                [
                    '$sort' => [
                        '_id.date'      => 1,
                        'seances_count' => -1,
                    ],
                ],
            ]);
        });
    }

    /**
     * Récupère les films en diffusion
     */
    public function scopeEnDiffusion($query)
    {
        return $query->where(FilmCatalogueSchema::STATUT_DIFFUSION, 'en_diffusion');
    }

    /**
     * Récupère les films par genre
     */
    public function scopeByGenre($query, string $genre)
    {
        return $query->where(FilmCatalogueSchema::GENRE, $genre);
    }

    /**
     * Récupère les films par classification
     */
    public function scopeByClassification($query, string $classification)
    {
        return $query->where(FilmCatalogueSchema::CLASSIFICATION, $classification);
    }

    /**
     * Recherche dans titre et description avec MongoDB regex
     */
    public function scopeSearchFilms($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where(FilmCatalogueSchema::TITRE, 'regex', new \MongoDB\BSON\Regex($search, 'i'))
                ->orWhere(FilmCatalogueSchema::DESCRIPTION, 'regex', new \MongoDB\BSON\Regex($search, 'i'));
        });
    }

    /**
     * Récupère les films avec note minimum
     */
    public function scopeMinNote($query, float $minNote)
    {
        return $query->where(FilmCatalogueSchema::NOTE_MOYENNE, '>=', $minNote);
    }

    /**
     * Récupère les films entre deux notes
     */
    public function scopeNoteBetween($query, float $minNote, float $maxNote)
    {
        return $query->whereBetween(FilmCatalogueSchema::NOTE_MOYENNE, [$minNote, $maxNote]);
    }

    /**
     * Récupère les films avec durée minimum
     */
    public function scopeMinDuration($query, int $minDuration)
    {
        return $query->where(FilmCatalogueSchema::DUREE, '>=', $minDuration);
    }

    /**
     * Récupère les films avec durée maximum
     */
    public function scopeMaxDuration($query, int $maxDuration)
    {
        return $query->where(FilmCatalogueSchema::DUREE, '<=', $maxDuration);
    }

    /**
     * Récupère les films par tranche de durée
     */
    public function scopeDurationBetween($query, int $minDuration, int $maxDuration)
    {
        return $query->whereBetween(FilmCatalogueSchema::DUREE, [$minDuration, $maxDuration]);
    }

    /**
     * Récupère les films sortis après une date
     */
    public function scopeReleasedAfter($query, DateTime $date)
    {
        return $query->where(FilmCatalogueSchema::DATE_SORTIE, '>=', $date);
    }

    /**
     * Récupère les films sortis avant une date
     */
    public function scopeReleasedBefore($query, DateTime $date)
    {
        return $query->where(FilmCatalogueSchema::DATE_SORTIE, '<=', $date);
    }

    /**
     * Récupère les films par réalisateur
     */
    public function scopeByDirector($query, string $director)
    {
        $regex = new \MongoDB\BSON\Regex($director, 'i');

        return $query->where(FilmCatalogueSchema::REALISATEUR, 'regex', $regex);
    }

    /**
     * Récupère les films avec un acteur spécifique
     */
    public function scopeWithActor($query, string $actor)
    {
        $regex = new \MongoDB\BSON\Regex($actor, 'i');

        return $query->where(FilmCatalogueSchema::ACTEURS_PRINCIPAUX, 'regex', $regex);
    }

    /**
     * Récupère les films avec minimum d'avis
     */
    public function scopeMinReviews($query, int $minReviews)
    {
        return $query->where(FilmCatalogueSchema::NOMBRE_AVIS, '>=', $minReviews);
    }

    /**
     * Récupère les films dans plusieurs cinémas
     *
     * @param array<string> $cinemaIds
     */
    public function scopeInCinemas($query, array $cinemaIds)
    {
        return $query->whereIn(FilmCatalogueSchema::CINEMAS_DIFFUSION . '.cinema_id', $cinemaIds);
    }

    /**
     * Récupère les films dans une ville spécifique
     */
    public function scopeInCity($query, string $city)
    {
        $regex = new \MongoDB\BSON\Regex($city, 'i');

        return $query->where(FilmCatalogueSchema::CINEMAS_DIFFUSION . '.ville', 'regex', $regex);
    }

    /**
     * Récupère les films diffusés dans un cinéma avec MongoDB array query
     */
    public function scopeByCinema($query, string $cinemaId)
    {
        return $query->where(FilmCatalogueSchema::CINEMAS_DIFFUSION . '.cinema_id', $cinemaId);
    }

    /**
     * Récupère les films avec séances dans une période donnée
     */
    public function scopeWithSeancesBetween($query, DateTime $startDate, DateTime $endDate)
    {
        return $query->where(FilmCatalogueSchema::PROCHAINES_SEANCES . '.date_heure_debut', '>=', $startDate)
            ->where(FilmCatalogueSchema::PROCHAINES_SEANCES . '.date_heure_debut', '<=', $endDate);
    }

    /**
     * Recherche avancée avec MongoDB regex optimisé
     */
    public function scopeSearchAdvanced($query, string $search)
    {
        $regex = new \MongoDB\BSON\Regex($search, 'i');

        return $query->where(function ($q) use ($regex) {
            $q->where(FilmCatalogueSchema::TITRE, 'regex', $regex)
                ->orWhere(FilmCatalogueSchema::DESCRIPTION, 'regex', $regex)
                ->orWhere(FilmCatalogueSchema::REALISATEUR, 'regex', $regex)
                ->orWhere(FilmCatalogueSchema::ACTEURS_PRINCIPAUX, 'regex', $regex);
        });
    }

    /**
     * Récupère les films populaires avec note minimale
     */
    public function scopePopularWithMinRating($query, float $minRating = 3.0)
    {
        return $query->where(FilmCatalogueSchema::NOTE_MOYENNE, '>=', $minRating)
            ->orderBy(FilmCatalogueSchema::NOTE_MOYENNE, 'desc')
            ->orderBy(FilmCatalogueSchema::NOMBRE_AVIS, 'desc');
    }

    /**
     * Récupère les films avec leurs statistiques de séances via aggregation
     */
    public function scopeWithSeanceStats($query)
    {
        return $query->addSelect([
            FilmCatalogueSchema::FILM_ID,
            FilmCatalogueSchema::TITRE,
            FilmCatalogueSchema::GENRE,
            FilmCatalogueSchema::DUREE,
            FilmCatalogueSchema::NOTE_MOYENNE,
            FilmCatalogueSchema::NOMBRE_AVIS,
            FilmCatalogueSchema::STATUT_DIFFUSION,
            'total_seances' => function ($q) {
                return $q->raw('{ $size: "$' . FilmCatalogueSchema::PROCHAINES_SEANCES . '" }');
            },
            'cinemas_count' => function ($q) {
                return $q->raw('{ $size: "$' . FilmCatalogueSchema::CINEMAS_DIFFUSION . '" }');
            },
        ]);
    }

    /**
     * Trie par note moyenne (descendant)
     */
    public function scopePopular($query)
    {
        return $query->orderBy(FilmCatalogueSchema::NOTE_MOYENNE, 'desc');
    }

    /**
     * Trie par date de sortie (récents d'abord)
     */
    public function scopeRecent($query)
    {
        return $query->orderBy(FilmCatalogueSchema::DATE_SORTIE, 'desc');
    }

    /**
     * Scope pour filtrer seulement les films actifs
     */
    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    /**
     * Scope pour filtrer les films en cours d'exploitation
     */
    public function scopeEnExploitation($query)
    {
        $now = now();

        return $query->actif()
            ->where('date_sortie', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('date_fin_exploitation')
                    ->orWhere('date_fin_exploitation', '>=', $now);
            });
    }

    /**
     * Scope pour filtrer avec séances disponibles
     */
    public function scopeWithAvailableSeancesOld($query)
    {
        return $query->whereNotNull('cinemas_diffusion')
            ->where('cinemas_diffusion', '!=', []);
    }

    /**
     * Retourne la durée formatée
     */
    public function getFormattedDuration(): string
    {
        $duration = $this->duree_minutes ?? $this->duree ?? 0;
        $hours    = intval($duration / 60);
        $minutes  = $duration % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}min";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}min";
        }
    }

    /**
     * Vérifie si le film est actuellement à l'affiche
     */
    public function isInTheaters(): bool
    {
        if (!$this->est_actif) {
            return false;
        }

        $now = now();

        return $this->date_sortie <= $now &&
               (!$this->date_fin_exploitation || $this->date_fin_exploitation >= $now);
    }

    protected function casts(): array
    {
        return [
            'realisateurs'                    => 'array',
            'acteurs_principaux'              => 'array',
            'genres'                          => 'array',
            'sous_titres'                     => 'array',
            'images_additionnelles'           => 'array',
            'metadonnees_techniques'          => 'array',
            'duree_minutes'                   => 'integer',
            'note_critique'                   => 'float',
            'note_public'                     => 'float',
            'note_moyenne_avis'               => 'float',
            'nombre_avis'                     => 'integer',
            'est_actif'                       => 'boolean',
            'date_fin_exploitation'           => 'datetime',
            FilmCatalogueSchema::DUREE        => 'integer',
            FilmCatalogueSchema::DATE_SORTIE  => 'datetime',
            FilmCatalogueSchema::NOTE_MOYENNE => 'float',
            FilmCatalogueSchema::NOMBRE_AVIS  => 'integer',
            FilmCatalogueSchema::CREATED_AT   => 'datetime',
            FilmCatalogueSchema::UPDATED_AT   => 'datetime',
            FilmCatalogueSchema::DELETED_AT   => 'datetime',
        ];
    }
}
