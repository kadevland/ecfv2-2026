<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\ReadModels;

use MongoDB\Laravel\Eloquent\Model;
use App\Infrastructure\Database\Schemas\Stats\StatsSchema;

/**
 * Modèle MongoDB pour la collection stats_yearly
 * Collection pour les statistiques annuelles
 */
class StatsYearly extends Model
{
    public $connection = StatsSchema::CONNECTION;

    protected $collection = StatsSchema::STATS_YEARLY;

    protected $fillable = [
        StatsSchema::ANNEE,
        StatsSchema::TOTAL_VENTES,
        StatsSchema::TOTAL_BILLETS_VENDUS,
        StatsSchema::REVENUS_TOTAL,
        StatsSchema::TAUX_OCCUPATION_MOYEN,
        StatsSchema::FILMS_TOP_ANNEE,
        StatsSchema::CINEMAS_PERFORMANCE,
        StatsSchema::EVOLUTION_MENSUELLE,
        StatsSchema::OBJECTIFS_ANNUELS,
        StatsSchema::COMPARAISON_ANNEE_PRECEDENTE,
    ];

    protected $casts = [
        StatsSchema::ANNEE                        => 'integer',
        StatsSchema::TOTAL_VENTES                 => 'integer',
        StatsSchema::TOTAL_BILLETS_VENDUS         => 'integer',
        StatsSchema::REVENUS_TOTAL                => 'float',
        StatsSchema::TAUX_OCCUPATION_MOYEN        => 'float',
        StatsSchema::FILMS_TOP_ANNEE              => 'array',
        StatsSchema::CINEMAS_PERFORMANCE          => 'array',
        StatsSchema::EVOLUTION_MENSUELLE          => 'array',
        StatsSchema::OBJECTIFS_ANNUELS            => 'array',
        StatsSchema::COMPARAISON_ANNEE_PRECEDENTE => 'array',
        StatsSchema::CREATED_AT                   => 'datetime',
        StatsSchema::UPDATED_AT                   => 'datetime',
    ];

    protected $dates = [
        StatsSchema::CREATED_AT,
        StatsSchema::UPDATED_AT,
    ];

    /**
     * Récupère les stats par année
     */
    public function scopeByAnnee($query, int $annee)
    {
        return $query->where(StatsSchema::ANNEE, $annee);
    }

    /**
     * Récupère les stats récentes
     */
    public function scopeRecent($query)
    {
        return $query->orderBy(StatsSchema::ANNEE, 'desc');
    }

    /**
     * Récupère les N dernières années
     */
    public function scopeLastYears($query, int $count = 5)
    {
        return $query->orderBy(StatsSchema::ANNEE, 'desc')
            ->limit($count);
    }
}
