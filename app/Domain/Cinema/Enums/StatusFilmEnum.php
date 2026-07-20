<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Enums;

enum StatusFilmEnum: string
{
    case A_VENIR  = 'A_VENIR';
    case EN_SALLE = 'EN_SALLE';
    case ARCHIVE  = 'ARCHIVE';
    case SUSPENDU = 'SUSPENDU';

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
            self::A_VENIR  => 'À venir',
            self::EN_SALLE => 'En salle',
            self::ARCHIVE  => 'Archivé',
            self::SUSPENDU => 'Suspendu',
        };
    }

    /**
     * Vérifier si le film est visible au public
     */
    public function isPublic(): bool
    {
        return in_array($this, [self::A_VENIR, self::EN_SALLE]);
    }

    /**
     * Vérifier si on peut réserver des séances
     */
    public function canBook(): bool
    {
        return $this === self::EN_SALLE;
    }
}
