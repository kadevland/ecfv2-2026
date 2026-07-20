<?php

declare(strict_types=1);

namespace App\Domain\Shared\Enums;

enum ThemeUIEnum: string
{
    case AUTO  = 'auto';
    case LIGHT = 'light';
    case DARK  = 'dark';

    /**
     * Thème par défaut
     */
    public static function default(): self
    {
        return self::AUTO;
    }

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
            array_map(fn ($case) => $case->icon() . ' ' . $case->label(), self::cases())
        );
    }

    /**
     * Obtenir le libellé français
     */
    public function label(): string
    {
        return match ($this) {
            self::AUTO  => 'Automatique',
            self::LIGHT => 'Clair',
            self::DARK  => 'Sombre',
        };
    }

    /**
     * Obtenir l'icône pour l'affichage
     */
    public function icon(): string
    {
        return match ($this) {
            self::AUTO  => '🔄',
            self::LIGHT => '☀️',
            self::DARK  => '🌙',
        };
    }

    /**
     * Obtenir la classe CSS correspondante
     */
    public function cssClass(): string
    {
        return match ($this) {
            self::AUTO  => 'theme-auto',
            self::LIGHT => 'theme-light',
            self::DARK  => 'theme-dark',
        };
    }
}
