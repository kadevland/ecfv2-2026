<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\ReadModels;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Infrastructure\Database\Schemas\Cinema\SeancePublicSchema;

/**
 * Modèle MongoDB pour la collection seance_publics
 * Collection optimisée pour l'affichage public des séances
 */
class SeancePublic extends Model
{
    use SoftDeletes;

    public $connection = SeancePublicSchema::CONNECTION;

    protected $collection = SeancePublicSchema::COLLECTION;

    protected $fillable = [
        SeancePublicSchema::SEANCE_ID,
        SeancePublicSchema::FILM_ID,
        SeancePublicSchema::SALLE_ID,
        SeancePublicSchema::CINEMA_ID,
        SeancePublicSchema::FILM_TITRE,
        SeancePublicSchema::SALLE_NOM,
        SeancePublicSchema::CINEMA_NOM,
        SeancePublicSchema::DATE_HEURE_DEBUT,
        SeancePublicSchema::DATE_HEURE_FIN,
        SeancePublicSchema::VERSION,
        SeancePublicSchema::TECHNOLOGIES,
        SeancePublicSchema::TARIFICATION,
        SeancePublicSchema::PLACES_TOTALES,
        SeancePublicSchema::PLACES_DISPONIBLES,
        SeancePublicSchema::EST_COMPLETE,
        SeancePublicSchema::PLACEMENT_LIBRE,
        SeancePublicSchema::STATUT,
        '_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        SeancePublicSchema::DATE_HEURE_DEBUT   => 'datetime',
        SeancePublicSchema::DATE_HEURE_FIN     => 'datetime',
        SeancePublicSchema::PLACES_TOTALES     => 'integer',
        SeancePublicSchema::PLACES_DISPONIBLES => 'integer',
        SeancePublicSchema::EST_COMPLETE       => 'boolean',
        SeancePublicSchema::PLACEMENT_LIBRE    => 'boolean',
        SeancePublicSchema::TECHNOLOGIES       => 'array',
        SeancePublicSchema::TARIFICATION       => 'array',
        SeancePublicSchema::CREATED_AT         => 'datetime',
        SeancePublicSchema::UPDATED_AT         => 'datetime',
        SeancePublicSchema::DELETED_AT         => 'datetime',
    ];

    protected $dates = [
        SeancePublicSchema::DATE_HEURE_DEBUT,
        SeancePublicSchema::DATE_HEURE_FIN,
        SeancePublicSchema::CREATED_AT,
        SeancePublicSchema::UPDATED_AT,
        SeancePublicSchema::DELETED_AT,
    ];

    /**
     * Récupère les séances actives
     */
    public function scopeActive($query)
    {
        return $query->where(SeancePublicSchema::STATUT, 'PROGRAMMEE');
    }

    /**
     * Récupère les séances d'un film
     */
    public function scopeByFilm($query, string $filmId)
    {
        return $query->where(SeancePublicSchema::FILM_ID, $filmId);
    }

    /**
     * Récupère les séances d'un cinéma
     */
    public function scopeByCinema($query, string $cinemaId)
    {
        return $query->where(SeancePublicSchema::CINEMA_ID, $cinemaId);
    }

    /**
     * Récupère les séances avec places disponibles
     */
    public function scopeAvailable($query)
    {
        return $query->where(SeancePublicSchema::PLACES_DISPONIBLES, '>', 0)
            ->where(SeancePublicSchema::EST_COMPLETE, false);
    }

    /**
     * Récupère les séances d'une période
     */
    public function scopeBetweenDates($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween(SeancePublicSchema::DATE_HEURE_DEBUT, [$dateDebut, $dateFin]);
    }

    /**
     * Récupère les séances à venir
     */
    public function scopeFuture($query)
    {
        return $query->where(SeancePublicSchema::DATE_HEURE_DEBUT, '>=', now());
    }

    /**
     * Trie par date de début
     */
    public function scopeOrderByDate($query, string $direction = 'asc')
    {
        return $query->orderBy(SeancePublicSchema::DATE_HEURE_DEBUT, $direction);
    }

    /**
     * Filtre par version
     */
    public function scopeByVersion($query, string $version)
    {
        return $query->where(SeancePublicSchema::VERSION, $version);
    }

    /**
     * Récupère les séances complètes
     */
    public function scopeComplete($query)
    {
        return $query->where(SeancePublicSchema::EST_COMPLETE, true);
    }

    /**
     * Récupère les séances avec placement libre
     */
    public function scopeFreeSeating($query)
    {
        return $query->where(SeancePublicSchema::PLACEMENT_LIBRE, true);
    }

    /**
     * Récupère les séances d'aujourd'hui
     */
    public function scopeToday($query)
    {
        return $query->whereBetween(
            SeancePublicSchema::DATE_HEURE_DEBUT,
            [now()->startOfDay(), now()->endOfDay()]
        );
    }

    /**
     * Récupère les séances de cette semaine
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween(
            SeancePublicSchema::DATE_HEURE_DEBUT,
            [now()->startOfWeek(), now()->endOfWeek()]
        );
    }

    /**
     * Vérifie si la séance est complète
     */
    public function isComplete(): bool
    {
        return $this->est_complete || $this->places_disponibles <= 0;
    }

    /**
     * Vérifie si la séance est à venir
     */
    public function isUpcoming(): bool
    {
        return $this->date_heure_debut >= now();
    }

    /**
     * Obtient le taux de remplissage
     */
    public function getOccupancyRate(): float
    {
        if ($this->places_totales <= 0) {
            return 0;
        }

        $occupied = $this->places_totales - $this->places_disponibles;
        return ($occupied / $this->places_totales) * 100;
    }

    /**
     * Obtient les places disponibles formatées
     */
    public function getFormattedAvailableSeats(): string
    {
        if ($this->est_complete) {
            return 'Complet';
        }

        if ($this->places_disponibles <= 10) {
            return "Dernières places ({$this->places_disponibles})";
        }

        return (string) $this->places_disponibles;
    }

    /**
     * Obtient l'heure de début formatée
     */
    public function getFormattedStartTime(): string
    {
        return $this->date_heure_debut->format('H:i');
    }

    /**
     * Obtient la date de début formatée
     */
    public function getFormattedStartDate(): string
    {
        return $this->date_heure_debut->format('d/m/Y');
    }

    /**
     * Vérifie si la séance est dans moins de X heures
     */
    public function isWithinHours(int $hours): bool
    {
        return $this->date_heure_debut->diffInHours(now()) <= $hours;
    }

    /**
     * Obtient la durée de la séance en minutes
     */
    public function getDurationMinutes(): int
    {
        return $this->date_heure_debut->diffInMinutes($this->date_heure_fin);
    }

    /**
     * Obtient la durée formatée
     */
    public function getFormattedDuration(): string
    {
        $minutes = $this->getDurationMinutes();
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0 && $remainingMinutes > 0) {
            return "{$hours}h {$remainingMinutes}min";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$remainingMinutes}min";
        }
    }

    /**
     * Scope pour rechercher des séances par texte
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $regex = new \MongoDB\BSON\Regex($search, 'i');
            $q->where(SeancePublicSchema::FILM_TITRE, 'regex', $regex)
                ->orWhere(SeancePublicSchema::CINEMA_NOM, 'regex', $regex)
                ->orWhere(SeancePublicSchema::SALLE_NOM, 'regex', $regex);
        });
    }

    /**
     * Récupère les séances groupées par film
     */
    public static function groupByFilmWithCount($dateStart, $dateEnd)
    {
        return static::raw(function ($collection) use ($dateStart, $dateEnd) {
            return $collection->aggregate([
                [
                    '$match' => [
                        SeancePublicSchema::DATE_HEURE_DEBUT => [
                            '$gte' => new \MongoDB\BSON\UTCDateTime(strtotime($dateStart) * 1000),
                            '$lte' => new \MongoDB\BSON\UTCDateTime(strtotime($dateEnd) * 1000),
                        ],
                        SeancePublicSchema::STATUT => 'PROGRAMMEE',
                    ],
                ],
                [
                    '$group' => [
                        '_id' => '$' . SeancePublicSchema::FILM_ID,
                        'film_titre' => ['$first' => '$' . SeancePublicSchema::FILM_TITRE],
                        'seances_count' => ['$sum' => 1],
                        'cinemas' => ['$addToSet' => '$' . SeancePublicSchema::CINEMA_ID],
                        'total_places' => ['$sum' => '$' . SeancePublicSchema::PLACES_TOTALES],
                        'available_places' => ['$sum' => '$' . SeancePublicSchema::PLACES_DISPONIBLES],
                        'first_seance' => ['$min' => '$' . SeancePublicSchema::DATE_HEURE_DEBUT],
                        'last_seance' => ['$max' => '$' . SeancePublicSchema::DATE_HEURE_DEBUT],
                    ],
                ],
                [
                    '$sort' => ['seances_count' => -1],
                ],
            ]);
        });
    }

    /**
     * Récupère les séances avec statistiques
     */
    public static function getSeanceStats($cinemaId = null)
    {
        return static::raw(function ($collection) use ($cinemaId) {
            $match = [
                SeancePublicSchema::STATUT => 'PROGRAMMEE',
                SeancePublicSchema::DATE_HEURE_DEBUT => [
                    '$gte' => new \MongoDB\BSON\UTCDateTime(now()->startOfDay()->getTimestamp() * 1000),
                    '$lte' => new \MongoDB\BSON\UTCDateTime(now()->endOfDay()->addWeek()->getTimestamp() * 1000),
                ],
            ];

            if ($cinemaId) {
                $match[SeancePublicSchema::CINEMA_ID] = $cinemaId;
            }

            return $collection->aggregate([
                ['$match' => $match],
                [
                    '$group' => [
                        '_id' => null,
                        'total_seances' => ['$sum' => 1],
                        'total_places' => ['$sum' => '$' . SeancePublicSchema::PLACES_TOTALES],
                        'available_places' => ['$sum' => '$' . SeancePublicSchema::PLACES_DISPONIBLES],
                        'complete_seances' => ['$sum' => ['$cond' => [['$eq' => ['$' . SeancePublicSchema::EST_COMPLETE, true]], 1, 0]]],
                        'avg_occupancy' => ['$avg' => [
                            '$divide' => [
                                ['$subtract' => ['$' . SeancePublicSchema::PLACES_TOTALES, '$' . SeancePublicSchema::PLACES_DISPONIBLES]],
                                '$' . SeancePublicSchema::PLACES_TOTALES
                            ]
                        ]],
                    ],
                ],
            ]);
        });
    }
}
