<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\ReadModels;

use MongoDB\Laravel\Eloquent\Model;
use App\Infrastructure\Database\Schemas\Stats\StatsSchema;

/**
 * Modèle MongoDB pour la collection stats_realtime
 * Collection pour les statistiques temps réel du dashboard
 */
class StatsRealtime extends Model
{
    public $connection = StatsSchema::CONNECTION;

    protected $collection = StatsSchema::STATS_REALTIME;

    protected $fillable = [
        StatsSchema::TIMESTAMP,
        StatsSchema::VENTES_JOUR,
        StatsSchema::RESERVATIONS_ACTIVES,
        StatsSchema::TAUX_OCCUPATION_GLOBAL,
        StatsSchema::REVENUS_JOUR,
        StatsSchema::FILMS_PERFORMANCE,
        StatsSchema::CINEMAS_PERFORMANCE,
        StatsSchema::ALERTES,
    ];

    protected $casts = [
        StatsSchema::TIMESTAMP              => 'datetime',
        StatsSchema::VENTES_JOUR            => 'integer',
        StatsSchema::RESERVATIONS_ACTIVES   => 'integer',
        StatsSchema::TAUX_OCCUPATION_GLOBAL => 'float',
        StatsSchema::REVENUS_JOUR           => 'float',
        StatsSchema::FILMS_PERFORMANCE      => 'array',
        StatsSchema::CINEMAS_PERFORMANCE    => 'array',
        StatsSchema::ALERTES                => 'array',
        StatsSchema::CREATED_AT             => 'datetime',
        StatsSchema::UPDATED_AT             => 'datetime',
    ];

    protected $dates = [
        StatsSchema::TIMESTAMP,
        StatsSchema::CREATED_AT,
        StatsSchema::UPDATED_AT,
    ];

    /**
     * Récupère les stats les plus récentes
     */
    public function scopeLatest($query)
    {
        return $query->orderBy(StatsSchema::TIMESTAMP, 'desc');
    }

    /**
     * Récupère les stats d'une période
     */
    public function scopeBetween($query, $start, $end)
    {
        return $query->whereBetween(StatsSchema::TIMESTAMP, [$start, $end]);
    }

    /**
     * Récupère les stats avec alertes
     */
    public function scopeWithAlerts($query)
    {
        return $query->where(StatsSchema::ALERTES, '!=', []);
    }
}
