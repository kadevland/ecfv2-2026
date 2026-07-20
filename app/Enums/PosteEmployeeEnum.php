<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Enum pour les postes des employés de cinéma
 * Métier cinéma : postes standards dans une chaîne de cinémas
 */
enum PosteEmployeeEnum: string
{
    case CAISSIER              = 'Caissier';
    case PROJECTIONNISTE       = 'Projectionniste';
    case RESPONSABLE_SALLE     = 'Responsable Salle';
    case TECHNICIEN            = 'Technicien';
    case AGENT_ACCUEIL         = "Agent d'Accueil";
    case RESPONSABLE_BAR       = 'Responsable Bar';
    case RESPONSABLE_TECHNIQUE = 'Responsable Technique';
    case DIRECTEUR_ADJOINT     = 'Directeur Adjoint';
    case DIRECTEUR             = 'Directeur';
    case COMPTABLE             = 'Comptable';
    case RESPONSABLE_RH        = 'Responsable RH';
    case CHEF_SECTEUR          = 'Chef de Secteur';

    /**
     * Retourne tous les postes disponibles
     *
     * @return array<string>
     */
    public static function allValues(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }

    /**
     * Postes de direction/management
     *
     * @return array<self>
     */
    public static function managementPosts(): array
    {
        return [
            self::DIRECTEUR,
            self::DIRECTEUR_ADJOINT,
            self::RESPONSABLE_TECHNIQUE,
            self::RESPONSABLE_RH,
            self::CHEF_SECTEUR,
        ];
    }

    /**
     * Postes techniques
     *
     * @return array<self>
     */
    public static function technicalPosts(): array
    {
        return [
            self::PROJECTIONNISTE,
            self::TECHNICIEN,
            self::RESPONSABLE_TECHNIQUE,
        ];
    }

    /**
     * Postes d'accueil/service client
     *
     * @return array<self>
     */
    public static function customerServicePosts(): array
    {
        return [
            self::CAISSIER,
            self::AGENT_ACCUEIL,
            self::RESPONSABLE_SALLE,
        ];
    }
}
