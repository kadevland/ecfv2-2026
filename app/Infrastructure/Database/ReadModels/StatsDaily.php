<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\ReadModels;

use MongoDB\Laravel\Eloquent\Model;
use App\Infrastructure\Database\Schemas\Stats\StatsSchema;

/**
 * Modèle MongoDB pour la collection stats_daily
 * Collection pour les statistiques quotidiennes
 */
class StatsDaily extends Model
{
    public $connection = StatsSchema::CONNECTION;

    protected $collection = StatsSchema::STATS_DAILY;

    protected $fillable = [
        StatsSchema::DATE,
        StatsSchema::TOTAL_VENTES,
        StatsSchema::TOTAL_BILLETS_VENDUS,
        StatsSchema::REVENUS_TOTAL,
        StatsSchema::TAUX_OCCUPATION_MOYEN,
        StatsSchema::FILMS_STATS,
        StatsSchema::CINEMAS_STATS,
        StatsSchema::SEANCES_STATS,
    ];

    protected $casts = [
        StatsSchema::DATE                  => 'date',
        StatsSchema::TOTAL_VENTES          => 'integer',
        StatsSchema::TOTAL_BILLETS_VENDUS  => 'integer',
        StatsSchema::REVENUS_TOTAL         => 'float',
        StatsSchema::TAUX_OCCUPATION_MOYEN => 'float',
        StatsSchema::FILMS_STATS           => 'array',
        StatsSchema::CINEMAS_STATS         => 'array',
        StatsSchema::SEANCES_STATS         => 'array',
        StatsSchema::CREATED_AT            => 'datetime',
        StatsSchema::UPDATED_AT            => 'datetime',
    ];

    protected $dates = [
        StatsSchema::DATE,
        StatsSchema::CREATED_AT,
        StatsSchema::UPDATED_AT,
    ];

    /**
     * Récupère les stats par date
     */
    public function scopeByDate($query, $date)
    {
        return $query->where(StatsSchema::DATE, $date);
    }

    /**
     * Récupère les stats d'une période
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween(StatsSchema::DATE, [$startDate, $endDate]);
    }

    /**
     * Trie par date (récent d'abord)
     */
    public function scopeRecent($query)
    {
        return $query->orderBy(StatsSchema::DATE, 'desc');
    }
}
