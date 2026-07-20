<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Enums;

enum TypeTarifEnum: string
{
    case NORMAL   = 'normal';
    case REDUIT   = 'reduit';
    case ENFANT   = 'enfant';
    case SENIOR   = 'senior';
    case ETUDIANT = 'etudiant';
    case GROUPE   = 'groupe';

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
            self::NORMAL   => 'Tarif normal',
            self::REDUIT   => 'Tarif réduit',
            self::ENFANT   => 'Tarif enfant',
            self::SENIOR   => 'Tarif senior',
            self::ETUDIANT => 'Tarif étudiant',
            self::GROUPE   => 'Tarif groupe',
        };
    }

    /**
     * Obtenir le pourcentage de réduction
     */
    public function getReductionPercent(): int
    {
        return match ($this) {
            self::NORMAL   => 0,
            self::REDUIT   => 20,
            self::ENFANT   => 30,
            self::SENIOR   => 25,
            self::ETUDIANT => 25,
            self::GROUPE   => 15,
        };
    }

    /**
     * Obtenir la description des conditions
     */
    public function conditions(): string
    {
        return match ($this) {
            self::NORMAL   => 'Plein tarif',
            self::REDUIT   => 'Sur présentation de justificatif',
            self::ENFANT   => 'Moins de 14 ans',
            self::SENIOR   => 'Plus de 65 ans',
            self::ETUDIANT => 'Sur présentation de la carte étudiant',
            self::GROUPE   => 'À partir de 10 personnes',
        };
    }
}
