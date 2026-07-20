<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Stats;

/**
 * Schema pour les collections de statistiques
 * Centralise les noms de champs et structures pour toutes les stats
 */
final class StatsSchema
{
    public const CONNECTION = 'mongodb';

    // Collections
    public const STATS_REALTIME = 'stats_realtime';

    public const STATS_DAILY = 'stats_daily';

    public const STATS_WEEKLY = 'stats_weekly';

    public const STATS_MONTHLY = 'stats_monthly';

    public const STATS_YEARLY = 'stats_yearly';

    // Champs communs
    public const TOTAL_VENTES = 'total_ventes';

    public const TOTAL_BILLETS_VENDUS = 'total_billets_vendus';

    public const REVENUS_TOTAL = 'revenus_total';

    public const TAUX_OCCUPATION_MOYEN = 'taux_occupation_moyen';

    public const FILMS_PERFORMANCE = 'films_performance';

    public const CINEMAS_PERFORMANCE = 'cinemas_performance';

    // Champs stats_realtime
    public const TIMESTAMP = 'timestamp';

    public const VENTES_JOUR = 'ventes_jour';

    public const RESERVATIONS_ACTIVES = 'reservations_actives';

    public const TAUX_OCCUPATION_GLOBAL = 'taux_occupation_global';

    public const REVENUS_JOUR = 'revenus_jour';

    public const ALERTES = 'alertes';

    // Champs stats_daily
    public const DATE = 'date';

    public const FILMS_STATS = 'films_stats';

    public const CINEMAS_STATS = 'cinemas_stats';

    public const SEANCES_STATS = 'seances_stats';

    // Champs stats_weekly
    public const SEMAINE = 'semaine';

    public const ANNEE = 'annee';

    public const DATE_DEBUT = 'date_debut';

    public const DATE_FIN = 'date_fin';

    public const FILMS_TOP = 'films_top';

    public const TENDANCES = 'tendances';

    // Champs stats_monthly
    public const MOIS = 'mois';

    public const FILMS_TOP_MOIS = 'films_top';

    public const COMPARAISON_MOIS_PRECEDENT = 'comparaison_mois_precedent';

    public const OBJECTIFS_ATTEINTS = 'objectifs_atteints';

    // Champs stats_yearly
    public const FILMS_TOP_ANNEE = 'films_top_annee';

    public const EVOLUTION_MENSUELLE = 'evolution_mensuelle';

    public const OBJECTIFS_ANNUELS = 'objectifs_annuels';

    public const COMPARAISON_ANNEE_PRECEDENTE = 'comparaison_annee_precedente';

    // Timestamps
    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    /**
     * Structure pour les performances de films
     */
    public static function filmPerformanceStructure(array $data): array
    {
        return [
            'film_id'         => $data['film_id'],
            'titre'           => $data['titre'],
            'ventes'          => (int) $data['ventes'],
            'revenus'         => (float) $data['revenus'],
            'taux_occupation' => (float) $data['taux_occupation'],
            'nombre_seances'  => (int) ($data['nombre_seances'] ?? 0),
        ];
    }

    /**
     * Structure pour les performances de cinémas
     */
    public static function cinemaPerformanceStructure(array $data): array
    {
        return [
            'cinema_id'       => $data['cinema_id'],
            'nom'             => $data['nom'],
            'ville'           => $data['ville'],
            'ventes'          => (int) $data['ventes'],
            'revenus'         => (float) $data['revenus'],
            'taux_occupation' => (float) $data['taux_occupation'],
            'nombre_seances'  => (int) ($data['nombre_seances'] ?? 0),
        ];
    }

    /**
     * Structure pour les alertes temps réel
     */
    public static function alerteStructure(array $data): array
    {
        return [
            'type'      => $data['type'], // 'low_attendance', 'high_demand', 'technical_issue'
            'message'   => $data['message'],
            'niveau'    => $data['niveau'], // 'info', 'warning', 'critical'
            'cinema_id' => $data['cinema_id'] ?? null,
            'seance_id' => $data['seance_id'] ?? null,
            'timestamp' => $data['timestamp'],
        ];
    }

    /**
     * Structure stats_realtime
     */
    public static function statsRealtimeStructure(array $data): array
    {
        return [
            self::TIMESTAMP              => $data['timestamp'],
            self::VENTES_JOUR            => (int) ($data['ventes_jour'] ?? 0),
            self::RESERVATIONS_ACTIVES   => (int) ($data['reservations_actives'] ?? 0),
            self::TAUX_OCCUPATION_GLOBAL => (float) ($data['taux_occupation_global'] ?? 0.0),
            self::REVENUS_JOUR           => (float) ($data['revenus_jour'] ?? 0.0),
            self::FILMS_PERFORMANCE      => $data['films_performance'] ?? [],
            self::CINEMAS_PERFORMANCE    => $data['cinemas_performance'] ?? [],
            self::ALERTES                => $data['alertes'] ?? [],
        ];
    }

    /**
     * Structure stats_daily
     */
    public static function statsDailyStructure(array $data): array
    {
        return [
            self::DATE                  => $data['date'],
            self::TOTAL_VENTES          => (int) ($data['total_ventes'] ?? 0),
            self::TOTAL_BILLETS_VENDUS  => (int) ($data['total_billets_vendus'] ?? 0),
            self::REVENUS_TOTAL         => (float) ($data['revenus_total'] ?? 0.0),
            self::TAUX_OCCUPATION_MOYEN => (float) ($data['taux_occupation_moyen'] ?? 0.0),
            self::FILMS_STATS           => $data['films_stats'] ?? [],
            self::CINEMAS_STATS         => $data['cinemas_stats'] ?? [],
            self::SEANCES_STATS         => $data['seances_stats'] ?? [],
        ];
    }

    /**
     * Structure stats_weekly
     */
    public static function statsWeeklyStructure(array $data): array
    {
        return [
            self::SEMAINE               => (int) $data['semaine'],
            self::ANNEE                 => (int) $data['annee'],
            self::DATE_DEBUT            => $data['date_debut'],
            self::DATE_FIN              => $data['date_fin'],
            self::TOTAL_VENTES          => (int) ($data['total_ventes'] ?? 0),
            self::TOTAL_BILLETS_VENDUS  => (int) ($data['total_billets_vendus'] ?? 0),
            self::REVENUS_TOTAL         => (float) ($data['revenus_total'] ?? 0.0),
            self::TAUX_OCCUPATION_MOYEN => (float) ($data['taux_occupation_moyen'] ?? 0.0),
            self::FILMS_TOP             => $data['films_top'] ?? [],
            self::CINEMAS_PERFORMANCE   => $data['cinemas_performance'] ?? [],
            self::TENDANCES             => $data['tendances'] ?? [],
        ];
    }

    /**
     * Structure stats_monthly
     */
    public static function statsMonthlyStructure(array $data): array
    {
        return [
            self::MOIS                       => (int) $data['mois'],
            self::ANNEE                      => (int) $data['annee'],
            self::TOTAL_VENTES               => (int) ($data['total_ventes'] ?? 0),
            self::TOTAL_BILLETS_VENDUS       => (int) ($data['total_billets_vendus'] ?? 0),
            self::REVENUS_TOTAL              => (float) ($data['revenus_total'] ?? 0.0),
            self::TAUX_OCCUPATION_MOYEN      => (float) ($data['taux_occupation_moyen'] ?? 0.0),
            self::FILMS_TOP                  => $data['films_top'] ?? [],
            self::CINEMAS_PERFORMANCE        => $data['cinemas_performance'] ?? [],
            self::COMPARAISON_MOIS_PRECEDENT => $data['comparaison_mois_precedent'] ?? [],
            self::OBJECTIFS_ATTEINTS         => $data['objectifs_atteints'] ?? [],
        ];
    }

    /**
     * Structure stats_yearly
     */
    public static function statsYearlyStructure(array $data): array
    {
        return [
            self::ANNEE                        => (int) $data['annee'],
            self::TOTAL_VENTES                 => (int) ($data['total_ventes'] ?? 0),
            self::TOTAL_BILLETS_VENDUS         => (int) ($data['total_billets_vendus'] ?? 0),
            self::REVENUS_TOTAL                => (float) ($data['revenus_total'] ?? 0.0),
            self::TAUX_OCCUPATION_MOYEN        => (float) ($data['taux_occupation_moyen'] ?? 0.0),
            self::FILMS_TOP_ANNEE              => $data['films_top_annee'] ?? [],
            self::CINEMAS_PERFORMANCE          => $data['cinemas_performance'] ?? [],
            self::EVOLUTION_MENSUELLE          => $data['evolution_mensuelle'] ?? [],
            self::OBJECTIFS_ANNUELS            => $data['objectifs_annuels'] ?? [],
            self::COMPARAISON_ANNEE_PRECEDENTE => $data['comparaison_annee_precedente'] ?? [],
        ];
    }
}
