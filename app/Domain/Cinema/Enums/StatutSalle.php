<?php

declare(strict_types=1);

namespace App\Domain\Cinema\Enums;

/**
 * Enum pour les statuts de salle
 */
enum StatutSalle: string
{
    case ACTIVE       = 'ACTIVE';
    case MAINTENANCE  = 'MAINTENANCE';
    case RENOVATION   = 'RENOVATION';
    case HORS_SERVICE = 'HORS_SERVICE';

    /**
     * Obtenir toutes les valeurs
     *
     * @return array<string>
     */
    public static function getValues(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    /**
     * Obtenir toutes les valeurs avec labels
     *
     * @return array<string, string>
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getLabel();
        }

        return $options;
    }

    /**
     * Obtenir le label français
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE       => 'Active',
            self::MAINTENANCE  => 'En maintenance',
            self::RENOVATION   => 'En rénovation',
            self::HORS_SERVICE => 'Hors service',
        };
    }

    /**
     * Obtenir la couleur pour les badges
     */
    public function getColor(): string
    {
        return match ($this) {
            self::ACTIVE       => 'green',
            self::MAINTENANCE  => 'yellow',
            self::RENOVATION   => 'orange',
            self::HORS_SERVICE => 'red',
        };
    }

    /**
     * Vérifier si la salle est utilisable
     */
    public function isUsable(): bool
    {
        return $this === self::ACTIVE;
    }

    /**
     * Vérifier si la salle nécessite une intervention
     */
    public function needsIntervention(): bool
    {
        return in_array($this, [self::MAINTENANCE, self::RENOVATION, self::HORS_SERVICE]);
    }
}
