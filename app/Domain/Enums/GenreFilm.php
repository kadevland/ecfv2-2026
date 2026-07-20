<?php

declare(strict_types=1);

namespace App\Domain\Enums;

enum GenreFilm: string
{
    case ACTION          = 'Action';
    case ANIMATION       = 'Animation';
    case AVENTURE        = 'Aventure';
    case BIOGRAPHIE      = 'Biographie';
    case COMEDIE         = 'Comédie';
    case CRIME           = 'Crime';
    case DOCUMENTAIRE    = 'Documentaire';
    case DRAME           = 'Drame';
    case FAMILLE         = 'Famille';
    case FANTASTIQUE     = 'Fantastique';
    case GUERRE          = 'Guerre';
    case HISTORIQUE      = 'Historique';
    case HORREUR         = 'Horreur';
    case MUSICAL         = 'Musical';
    case MYSTERE         = 'Mystère';
    case POLICIER        = 'Policier';
    case ROMANCE         = 'Romance';
    case SCIENCE_FICTION = 'Science-Fiction';
    case SPORT           = 'Sport';
    case THRILLER        = 'Thriller';
    case WESTERN         = 'Western';
    case AUTRE           = 'Autre';

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
     * @return array<GenreFilm>
     */
    public static function getPopularGenres(): array
    {
        return [
            self::ACTION,
            self::COMEDIE,
            self::DRAME,
            self::THRILLER,
            self::ROMANCE,
            self::SCIENCE_FICTION,
        ];
    }

    public function label(): string
    {
        return $this->value;
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::ACTION          => 'Films d\'action avec scènes dynamiques',
            self::ANIMATION       => 'Films d\'animation 2D/3D',
            self::AVENTURE        => 'Films d\'aventure et d\'exploration',
            self::BIOGRAPHIE      => 'Films biographiques sur des personnalités',
            self::COMEDIE         => 'Films comiques et humoristiques',
            self::CRIME           => 'Films sur le crime organisé',
            self::DOCUMENTAIRE    => 'Documentaires et films éducatifs',
            self::DRAME           => 'Films dramatiques et émotionnels',
            self::FAMILLE         => 'Films pour toute la famille',
            self::FANTASTIQUE     => 'Films fantastiques et surnaturels',
            self::GUERRE          => 'Films de guerre et conflits',
            self::HISTORIQUE      => 'Films sur des événements historiques',
            self::HORREUR         => 'Films d\'horreur et d\'épouvante',
            self::MUSICAL         => 'Films musicaux et comédies musicales',
            self::MYSTERE         => 'Films à mystère et énigmes',
            self::POLICIER        => 'Films policiers et d\'enquête',
            self::ROMANCE         => 'Films romantiques et sentimentaux',
            self::SCIENCE_FICTION => 'Films de science-fiction',
            self::SPORT           => 'Films sur le sport et les athlètes',
            self::THRILLER        => 'Films de suspense et thriller',
            self::WESTERN         => 'Films western et far-west',
            self::AUTRE           => 'Autres genres non classés',
        };
    }

    /**
     * @return array<string>
     */
    public function getTargetAudience(): array
    {
        return match ($this) {
            self::FAMILLE, self::ANIMATION => ['enfants', 'adolescents', 'adultes'],
            self::HORREUR, self::THRILLER => ['adolescents', 'adultes'],
            self::ROMANCE, self::DRAME => ['adultes'],
            self::ACTION, self::SCIENCE_FICTION => ['adolescents', 'adultes'],
            self::AUTRE => ['enfants', 'adolescents', 'adultes'],
            default     => ['adultes'],
        };
    }

    public function getColorClass(): string
    {
        return match ($this) {
            self::ACTION          => 'text-red-600',
            self::ANIMATION       => 'text-purple-600',
            self::AVENTURE        => 'text-orange-600',
            self::COMEDIE         => 'text-yellow-600',
            self::DRAME           => 'text-blue-600',
            self::HORREUR         => 'text-red-800',
            self::ROMANCE         => 'text-pink-600',
            self::SCIENCE_FICTION => 'text-cyan-600',
            self::AUTRE           => 'text-gray-500',
            default               => 'text-gray-600',
        };
    }
}
