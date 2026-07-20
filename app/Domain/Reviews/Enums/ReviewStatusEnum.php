<?php

declare(strict_types=1);

namespace App\Domain\Reviews\Enums;

enum ReviewStatusEnum: string
{
    case PENDING  = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

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
            self::PENDING  => 'En attente',
            self::APPROVED => 'Approuvé',
            self::REJECTED => 'Rejeté',
        };
    }

    /**
     * Obtenir la couleur pour l'affichage
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING  => 'yellow',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
        };
    }

    /**
     * Vérifier si l'avis est visible
     */
    public function isVisible(): bool
    {
        return $this === self::APPROVED;
    }
}
