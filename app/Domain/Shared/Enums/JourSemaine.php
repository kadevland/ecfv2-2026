<?php

declare(strict_types=1);

namespace App\Domain\Shared\Enums;

use InvalidArgumentException;

/**
 * Énumération des jours de la semaine
 */
enum JourSemaine: string
{
    case LUNDI    = 'lundi';
    case MARDI    = 'mardi';
    case MERCREDI = 'mercredi';
    case JEUDI    = 'jeudi';
    case VENDREDI = 'vendredi';
    case SAMEDI   = 'samedi';
    case DIMANCHE = 'dimanche';

    /**
     * Obtenir tous les jours de la semaine
     *
     * @return array<JourSemaine>
     */
    public static function getAll(): array
    {
        return self::cases();
    }

    /**
     * Obtenir les jours de semaine uniquement
     *
     * @return array<JourSemaine>
     */
    public static function getWeekdays(): array
    {
        return [
            self::LUNDI,
            self::MARDI,
            self::MERCREDI,
            self::JEUDI,
            self::VENDREDI,
        ];
    }

    /**
     * Obtenir les jours de weekend uniquement
     *
     * @return array<JourSemaine>
     */
    public static function getWeekend(): array
    {
        return [self::SAMEDI, self::DIMANCHE];
    }

    /**
     * Créer depuis le nom français
     */
    public static function fromFrenchName(string $name): self
    {
        $normalized = strtolower(trim($name));

        return match ($normalized) {
            'lundi'    => self::LUNDI,
            'mardi'    => self::MARDI,
            'mercredi' => self::MERCREDI,
            'jeudi'    => self::JEUDI,
            'vendredi' => self::VENDREDI,
            'samedi'   => self::SAMEDI,
            'dimanche' => self::DIMANCHE,
            default    => throw new InvalidArgumentException("Jour invalide: {$name}"),
        };
    }

    /**
     * Obtenir le libellé formaté
     */
    public function getLabel(): string
    {
        return ucfirst($this->value);
    }

    /**
     * Vérifier si c'est un jour de weekend
     */
    public function isWeekend(): bool
    {
        return in_array($this, [self::SAMEDI, self::DIMANCHE]);
    }

    /**
     * Vérifier si c'est un jour de semaine
     */
    public function isWeekday(): bool
    {
        return !$this->isWeekend();
    }

    /**
     * Obtenir le numéro du jour (1 = lundi, 7 = dimanche)
     */
    public function getNumber(): int
    {
        return match ($this) {
            self::LUNDI    => 1,
            self::MARDI    => 2,
            self::MERCREDI => 3,
            self::JEUDI    => 4,
            self::VENDREDI => 5,
            self::SAMEDI   => 6,
            self::DIMANCHE => 7,
        };
    }
}
