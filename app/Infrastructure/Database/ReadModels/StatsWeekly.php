<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\ReadModels;

use MongoDB\Laravel\Eloquent\Model;
use App\Infrastructure\Database\Schemas\Stats\StatsSchema;

/**
 * Modèle MongoDB pour la collection stats_weekly
 * Collection pour les statistiques hebdomadaires
 */
class StatsWeekly extends Model
{
    public $connection = StatsSchema::CONNECTION;

    protected $collection = StatsSchema::STATS_WEEKLY;

    protected $fillable = [
        StatsSchema::SEMAINE,
        StatsSchema::ANNEE,
        StatsSchema::DATE_DEBUT,
        StatsSchema::DATE_FIN,
        StatsSchema::TOTAL_VENTES,
        StatsSchema::TOTAL_BILLETS_VENDUS,
        StatsSchema::REVENUS_TOTAL,
        StatsSchema::TAUX_OCCUPATION_MOYEN,
        StatsSchema::FILMS_TOP,
        StatsSchema::CINEMAS_PERFORMANCE,
        StatsSchema::TENDANCES,
    ];

    protected $casts = [
        StatsSchema::SEMAINE               => 'integer',
        StatsSchema::ANNEE                 => 'integer',
        StatsSchema::DATE_DEBUT            => 'date',
        StatsSchema::DATE_FIN              => 'date',
        StatsSchema::TOTAL_VENTES          => 'integer',
        StatsSchema::TOTAL_BILLETS_VENDUS  => 'integer',
        StatsSchema::REVENUS_TOTAL         => 'float',
        StatsSchema::TAUX_OCCUPATION_MOYEN => 'float',
        StatsSchema::FILMS_TOP             => 'array',
        StatsSchema::CINEMAS_PERFORMANCE   => 'array',
        StatsSchema::TENDANCES             => 'array',
        StatsSchema::CREATED_AT            => 'datetime',
        StatsSchema::UPDATED_AT            => 'datetime',
    ];

    protected $dates = [
        StatsSchema::DATE_DEBUT,
        StatsSchema::DATE_FIN,
        StatsSchema::CREATED_AT,
        StatsSchema::UPDATED_AT,
    ];

    /**
     * Récupère les stats par semaine et année
     */
    public function scopeBySemaine($query, int $semaine, int $annee)
    {
        return $query->where(StatsSchema::SEMAINE, $semaine)
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
            ->orderBy(StatsSchema::SEMAINE, 'desc');
    }
}
