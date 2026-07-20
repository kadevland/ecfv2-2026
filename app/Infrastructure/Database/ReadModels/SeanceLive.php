<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\ReadModels;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Infrastructure\Database\Schemas\Cinema\SeanceLiveSchema;

/**
 * Modèle MongoDB pour la collection seances_live
 * Collection optimisée pour les requêtes temps réel des séances
 */
class SeanceLive extends Model
{
    use SoftDeletes;

    public $connection = SeanceLiveSchema::CONNECTION;

    protected $collection = SeanceLiveSchema::COLLECTION;

    protected $fillable = [
        SeanceLiveSchema::SEANCE_ID,
        SeanceLiveSchema::FILM_ID,
        SeanceLiveSchema::CINEMA_ID,
        SeanceLiveSchema::SALLE_ID,
        SeanceLiveSchema::TITRE_FILM,
        SeanceLiveSchema::NOM_CINEMA,
        SeanceLiveSchema::NOM_SALLE,
        SeanceLiveSchema::DATE_HEURE_DEBUT,
        SeanceLiveSchema::DATE_HEURE_FIN,
        SeanceLiveSchema::PLACES_TOTALES,
        SeanceLiveSchema::PLACES_VENDUES,
        SeanceLiveSchema::PLACES_RESERVEES,
        SeanceLiveSchema::PLACES_DISPONIBLES,
        SeanceLiveSchema::TARIFS,
        SeanceLiveSchema::STATUT,
        SeanceLiveSchema::VERSION,
        SeanceLiveSchema::SOUS_TITRES,
        SeanceLiveSchema::QUALITE_PROJECTION,
        '_id',
        'placement_libre',
        'taux_tva',
        'devise',
        'options_supplementaires',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        SeanceLiveSchema::DATE_HEURE_DEBUT   => 'datetime',
        SeanceLiveSchema::DATE_HEURE_FIN     => 'datetime',
        SeanceLiveSchema::PLACES_TOTALES     => 'integer',
        SeanceLiveSchema::PLACES_VENDUES     => 'integer',
        SeanceLiveSchema::PLACES_RESERVEES   => 'integer',
        SeanceLiveSchema::PLACES_DISPONIBLES => 'integer',
        SeanceLiveSchema::TARIFS             => 'array',
        SeanceLiveSchema::SOUS_TITRES        => 'boolean',
        SeanceLiveSchema::CREATED_AT         => 'datetime',
        SeanceLiveSchema::UPDATED_AT         => 'datetime',
        SeanceLiveSchema::DELETED_AT         => 'datetime',
    ];

    protected $dates = [
        SeanceLiveSchema::DATE_HEURE_DEBUT,
        SeanceLiveSchema::DATE_HEURE_FIN,
        SeanceLiveSchema::CREATED_AT,
        SeanceLiveSchema::UPDATED_AT,
        SeanceLiveSchema::DELETED_AT,
    ];

    /**
     * Récupère les séances actives
     */
    public function scopeActive($query)
    {
        return $query->where(SeanceLiveSchema::STATUT, 'active');
    }

    /**
     * Récupère les séances d'un film
     */
    public function scopeByFilm($query, string $filmId)
    {
        return $query->where(SeanceLiveSchema::FILM_ID, $filmId);
    }

    /**
     * Récupère les séances d'un cinéma
     */
    public function scopeByCinema($query, string $cinemaId)
    {
        return $query->where(SeanceLiveSchema::CINEMA_ID, $cinemaId);
    }

    /**
     * Récupère les séances avec places disponibles
     */
    public function scopeAvailable($query)
    {
        return $query->where(SeanceLiveSchema::PLACES_DISPONIBLES, '>', 0);
    }

    /**
     * Récupère les séances d'une période
     */
    public function scopeBetweenDates($query, $dateDebut, $dateFin)
    {
        return $query->whereBetween(SeanceLiveSchema::DATE_HEURE_DEBUT, [$dateDebut, $dateFin]);
    }

    /**
     * Récupère les séances à venir
     */
    public function scopeFuture($query)
    {
        return $query->where(SeanceLiveSchema::DATE_HEURE_DEBUT, '>=', now());
    }

    /**
     * Trie par date de début
     */
    public function scopeOrderByDate($query, string $direction = 'asc')
    {
        return $query->orderBy(SeanceLiveSchema::DATE_HEURE_DEBUT, $direction);
    }
}
