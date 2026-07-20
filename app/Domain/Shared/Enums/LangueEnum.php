<?php

declare(strict_types=1);

namespace App\Domain\Shared\Enums;

enum LangueEnum: string
{
    case FR = 'fr';
    case EN = 'en';
    case NL = 'nl';
    case DE = 'de';
    case ES = 'es';
    case IT = 'it';

    /**
     * Langue par défaut
     */
    public static function default(): self
    {
        return self::FR;
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
            array_map(fn ($case) => $case->flag() . ' ' . $case->label(), self::cases())
        );
    }

    /**
     * Obtenir le libellé français
     */
    public function label(): string
    {
        return match ($this) {
            self::FR => 'Français',
            self::EN => 'Anglais',
            self::NL => 'Néerlandais',
            self::DE => 'Allemand',
            self::ES => 'Espagnol',
            self::IT => 'Italien',
        };
    }

    /**
     * Obtenir le nom natif de la langue
     */
    public function nativeLabel(): string
    {
        return match ($this) {
            self::FR => 'Français',
            self::EN => 'English',
            self::NL => 'Nederlands',
            self::DE => 'Deutsch',
            self::ES => 'Español',
            self::IT => 'Italiano',
        };
    }

    /**
     * Obtenir le code ISO 639-1
     */
    public function isoCode(): string
    {
        return $this->value;
    }

    /**
     * Obtenir le drapeau emoji
     */
    public function flag(): string
    {
        return match ($this) {
            self::FR => '🇫🇷',
            self::EN => '🇬🇧',
            self::NL => '🇳🇱',
            self::DE => '🇩🇪',
            self::ES => '🇪🇸',
            self::IT => '🇮🇹',
        };
    }
}
