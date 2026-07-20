<?php

declare(strict_types=1);

namespace App\Domain\Shared\Enums;

/**
 * Énumération des versions de films disponibles
 * Utilisé pour les séances et la diffusion
 */
enum VersionFilm: string
{
    case VF = 'VF';           // Version Française
    case VO = 'VO';           // Version Originale

    /**
     * Obtenir toutes les versions avec leurs libellés
     *
     * @return array<array{value: string, label: string, short: string}>
     */
    public static function getOptions(): array
    {
        return array_map(
            fn (self $version) => [
                'value' => $version->value,
                'label' => $version->getLabel(),
                'short' => $version->getShortLabel(),
            ],
            self::cases()
        );
    }

    /**
     * Obtenir les versions les plus courantes
     *
     * @return array<VersionFilm>
     */
    public static function getCommonVersions(): array
    {
        return [self::VF, self::VO];
    }

    /**
     * Obtenir le libellé complet de la version
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::VF => 'Version Française',
            self::VO => 'Version Originale',
        };
    }

    /**
     * Obtenir le libellé court pour l'affichage
     */
    public function getShortLabel(): string
    {
        return $this->value;
    }

    /**
     * Vérifier si c'est une version française
     */
    public function isFrench(): bool
    {
        return $this === self::VF;
    }

    /**
     * Vérifier si c'est une version originale
     */
    public function isOriginal(): bool
    {
        return $this === self::VO;
    }
}
