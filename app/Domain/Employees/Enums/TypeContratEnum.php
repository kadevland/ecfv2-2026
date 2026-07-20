<?php

declare(strict_types=1);

namespace App\Domain\Employees\Enums;

enum TypeContratEnum: string
{
    case CDI           = 'CDI';
    case CDD           = 'CDD';
    case INTERIM       = 'INTERIM';
    case STAGE         = 'STAGE';
    case APPRENTISSAGE = 'APPRENTISSAGE';
    case FREELANCE     = 'FREELANCE';

    /**
     * Obtenir toutes les valeurs pour les validations
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Obtenir les options pour les selects HTML
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return array_combine(
            self::values(),
            array_map(fn ($case) => $case->label(), self::cases())
        );
    }

    /**
     * Obtenir le libellé français
     */
    public function label(): string
    {
        return match ($this) {
            self::CDI           => 'Contrat à durée indéterminée',
            self::CDD           => 'Contrat à durée déterminée',
            self::INTERIM       => 'Intérim',
            self::STAGE         => 'Stage',
            self::APPRENTISSAGE => 'Apprentissage',
            self::FREELANCE     => 'Freelance',
        };
    }

    /**
     * Obtenir l'abréviation
     */
    public function shortLabel(): string
    {
        return match ($this) {
            self::CDI           => 'CDI',
            self::CDD           => 'CDD',
            self::INTERIM       => 'Intérim',
            self::STAGE         => 'Stage',
            self::APPRENTISSAGE => 'Apprentissage',
            self::FREELANCE     => 'Freelance',
        };
    }

    /**
     * Vérifier si c'est un contrat permanent
     */
    public function isPermanent(): bool
    {
        return $this === self::CDI;
    }

    /**
     * Vérifier si c'est un contrat temporaire
     */
    public function isTemporary(): bool
    {
        return in_array($this, [self::CDD, self::INTERIM, self::STAGE, self::APPRENTISSAGE]);
    }

    /**
     * Obtenir la couleur pour l'affichage
     */
    public function color(): string
    {
        return match ($this) {
            self::CDI           => 'green',
            self::CDD           => 'blue',
            self::INTERIM       => 'orange',
            self::STAGE         => 'yellow',
            self::APPRENTISSAGE => 'cyan',
            self::FREELANCE     => 'purple',
        };
    }
}
