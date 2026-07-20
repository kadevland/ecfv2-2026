<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\ReadModels;

use MongoDB\Laravel\Eloquent\Model;
use App\Infrastructure\Database\Schemas\Stats\StatsSchema;

/**
 * Modèle MongoDB pour la collection stats_monthly
 * Collection pour les statistiques mensuelles
 */
class StatsMonthly extends Model
{
    public $connection = StatsSchema::CONNECTION;

    protected $collection = StatsSchema::STATS_MONTHLY;

    protected $fillable = [
        StatsSchema::MOIS,
        StatsSchema::ANNEE,
        StatsSchema::TOTAL_VENTES,
        StatsSchema::TOTAL_BILLETS_VENDUS,
        StatsSchema::REVENUS_TOTAL,
        StatsSchema::TAUX_OCCUPATION_MOYEN,
        StatsSchema::FILMS_TOP,
        StatsSchema::CINEMAS_PERFORMANCE,
        StatsSchema::COMPARAISON_MOIS_PRECEDENT,
        StatsSchema::OBJECTIFS_ATTEINTS,
    ];

    protected $casts = [
        StatsSchema::MOIS                       => 'integer',
        StatsSchema::ANNEE                      => 'integer',
        StatsSchema::TOTAL_VENTES               => 'integer',
        StatsSchema::TOTAL_BILLETS_VENDUS       => 'integer',
        StatsSchema::REVENUS_TOTAL              => 'float',
        StatsSchema::TAUX_OCCUPATION_MOYEN      => 'float',
        StatsSchema::FILMS_TOP                  => 'array',
        StatsSchema::CINEMAS_PERFORMANCE        => 'array',
        StatsSchema::COMPARAISON_MOIS_PRECEDENT => 'array',
        StatsSchema::OBJECTIFS_ATTEINTS         => 'array',
        StatsSchema::CREATED_AT                 => 'datetime',
        StatsSchema::UPDATED_AT                 => 'datetime',
    ];

    protected $dates = [
        StatsSchema::CREATED_AT,
        StatsSchema::UPDATED_AT,
    ];

    /**
     * Récupère les stats par mois et année
     */
    public function scopeByMois($query, int $mois, int $annee)
    {
        return $query->where(StatsSchema::MOIS, $mois)
            ->where(StatsSchema::ANNEE, $annee);
    }

    /**
     * Récupère les stats d'une année
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
        return $query->orderBy(StatsSchema::ANNEE, 'desc')
            ->orderBy(StatsSchema::MOIS, 'desc');
    }
}
