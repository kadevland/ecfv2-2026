<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Models\MongoDB;

use Illuminate\Support\Carbon;
use MongoDB\Laravel\Eloquent\Model;
use App\Infrastructure\Database\Schemas\Cinema\SeancePublicSchema;

/**
 * Modèle MongoDB pour la collection seances_public (read-side)
 *
 * @property string $seance_id
 * @property string $film_id
 * @property string $salle_id
 * @property string $cinema_id
 * @property string $film_titre
 * @property string $salle_nom
 * @property string $cinema_nom
 * @property Carbon $date_heure_debut
 * @property Carbon $date_heure_fin
 * @property string $version
 * @property array $technologies
 * @property array $tarification
 * @property string $statut
 * @property int $places_totales
 * @property int $places_disponibles
 * @property bool $est_complete
 * @property bool $placement_libre
 */
class SeancePublic extends Model
{
    /**
     * La connexion de base de données MongoDB
     */
    protected $connection = SeancePublicSchema::CONNECTION;

    /**
     * La collection MongoDB
     */
    protected $collection = SeancePublicSchema::COLLECTION;

    /**
     * La clé primaire MongoDB
     */
    protected $primaryKey = '_id';

    /**
     * Les attributs castés
     */
    protected $casts = [
        'technologies'       => 'array',
        'tarification'       => 'array',
        'places_totales'     => 'integer',
        'places_disponibles' => 'integer',
        'est_complete'       => 'boolean',
        'placement_libre'    => 'boolean',
        'date_heure_debut'   => 'datetime',
        'date_heure_fin'     => 'datetime',
    ];

    /**
     * Les attributs fillables pour mass assignment
     */
    protected $fillable = [
        '_id',
        'seance_id',
        'film_id',
        'salle_id',
        'cinema_id',
        'film_titre',
        'salle_nom',
        'cinema_nom',
        'date_heure_debut',
        'date_heure_fin',
        'version',
        'technologies',
        'tarifs',
        'tarification',
        'taux_tva',
        'devise',
        'statut',
        'places_totales',
        'places_vendues',
        'places_reservees',
        'places_disponibles',
        'est_complete',
        'placement_libre',
        'options_supplementaires',
        'sous_titres',
        'qualite_projection',
        'created_at',
        'updated_at',
    ];

    /**
     * Scope pour filtrer seulement les séances disponibles
     */
    public function scopeDisponible($query)
    {
        return $query->where('statut', 'PROGRAMMEE')
            ->where('places_disponibles', '>', 0);
    }

    /**
     * Scope pour filtrer les séances futures
     */
    public function scopeFuture($query)
    {
        return $query->where('date_heure_debut', '>', now());
    }

    /**
     * Scope pour filtrer par film
     */
    public function scopeByFilm($query, string $filmId)
    {
        return $query->where('film_id', $filmId);
    }

    /**
     * Scope pour filtrer par cinéma
     */
    public function scopeByCinema($query, string $cinemaId)
    {
        return $query->where('cinema_id', $cinemaId);
    }

    /**
     * Scope pour filtrer par version
     */
    public function scopeByVersion($query, string $version)
    {
        return $query->where('version', $version);
    }

    /**
     * Vérifie si la séance est complète
     */
    public function isComplete(): bool
    {
        return $this->places_disponibles <= 0;
    }

    /**
     * Vérifie si la séance est dans le futur
     */
    public function isFuture(): bool
    {
        return $this->date_heure_debut > now();
    }

    /**
     * Retourne le prix minimum
     */
    public function getPrixMinimum(): float
    {
        if (empty($this->tarification)) {
            return 0.0;
        }

        $tarifs = array_filter($this->tarification, 'is_numeric');

        return !empty($tarifs) ? min($tarifs) : 0.0;
    }

    /**
     * Retourne le prix maximum
     */
    public function getPrixMaximum(): float
    {
        if (empty($this->tarification)) {
            return 0.0;
        }

        $tarifs = array_filter($this->tarification, 'is_numeric');

        return !empty($tarifs) ? max($tarifs) : 0.0;
    }
}
