<?php

declare(strict_types=1);

namespace App\Infrastructure\Schemas\MongoDB;

final class FilmReviewSchema
{
    public const FILM_ID = 'film_id';

    public const TITRE_FILM = 'titre_film';

    public const TOTAL_REVIEWS = 'total_reviews';

    public const NOTE_MOYENNE = 'note_moyenne';

    public const DISTRIBUTION_NOTES = 'distribution_notes';

    public const AVIS = 'avis';

    public const UPDATED_AT = 'updated_at';

    /**
     * @return array<string, mixed>
     */
    public static function documentStructure(): array
    {
        return [
            self::FILM_ID            => '',
            self::TITRE_FILM         => '',
            self::TOTAL_REVIEWS      => 0,
            self::NOTE_MOYENNE       => 0.0,
            self::DISTRIBUTION_NOTES => self::distributionNotesStructure(),
            self::AVIS               => [],
            self::UPDATED_AT         => '',
        ];
    }

    /**
     * @return array<string, int>
     */
    public static function distributionNotesStructure(): array
    {
        return [
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '4' => 0,
            '5' => 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function avisStructure(): array
    {
        return [
            'id'            => '',
            'user_id'       => '',
            'note'          => 0,
            'titre'         => '',
            'commentaire'   => '',
            'date_creation' => '',
            'modere'        => false,
        ];
    }
}
