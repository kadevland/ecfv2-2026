<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas;

/**
 * Centralisation de tous les schémas PostgreSQL de l'application
 *
 * Cette classe contient toutes les définitions de schémas utilisées
 * dans l'application pour éviter la duplication et centraliser la configuration.
 */
final class DatabaseSchemas
{
    /**
     * Schéma par défaut PostgreSQL
     */
    public const PUBLIC = 'public';

    /**
     * Schéma pour les tables d'authentification et utilisateurs
     */
    public const AUTH = 'auth';

    /**
     * Schéma pour les tables liées aux cinémas
     */
    public const CINEMA = 'cinema';

    /**
     * Schéma pour les tables de profils utilisateurs et RGPD
     */
    public const PROFILES = 'profiles';

    /**
     * Schéma pour les tables de réservations et séances
     */
    public const RESERVATIONS = 'reservations';

    /**
     * Schéma pour les tables employés et incidents
     */
    public const EMPLOYEES = 'employees';

    /**
     * Retourne tous les schémas custom (sans public)
     *
     * @return array<string>
     */
    public static function getAllCustomSchemas(): array
    {
        return [
            self::AUTH,
            self::CINEMA,
            self::PROFILES,
            self::RESERVATIONS,
            self::EMPLOYEES,
        ];
    }

    /**
     * Retourne tous les schémas (y compris public)
     *
     * @return array<string>
     */
    public static function getAllSchemas(): array
    {
        return [
            self::PUBLIC,
            ...self::getAllCustomSchemas(),
        ];
    }

    /**
     * Vérifie si un schéma est valide
     */
    public static function isValidSchema(string $schema): bool
    {
        return in_array($schema, self::getAllSchemas(), true);
    }
}
