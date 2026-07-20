<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Schemas\Reviews;

/**
 * Schema pour la collection film_reviews
 * Centralise les noms de champs et structure
 */
final class FilmReviewSchema
{
    public const CONNECTION = 'mongodb';

    public const COLLECTION = 'film_reviews';

    // Champs principaux
    public const FILM_ID = 'film_id';

    public const TITRE_FILM = 'titre_film';

    public const TOTAL_REVIEWS = 'total_reviews';

    public const NOTE_MOYENNE = 'note_moyenne';

    public const DISTRIBUTION_NOTES = 'distribution_notes';

    public const AVIS = 'avis';

    // Champs des avis individuels
    public const AVIS_REVIEW_ID = 'avis.review_id';

    public const AVIS_USER_ID = 'avis.user_id';

    public const AVIS_USER_PRENOM = 'avis.user_prenom';

    public const AVIS_NOTE = 'avis.note';

    public const AVIS_COMMENTAIRE = 'avis.commentaire';

    public const AVIS_DATE_CREATION = 'avis.date_creation';

    public const AVIS_STATUT_MODERATION = 'avis.statut_moderation';

    public const AVIS_LIKES_COUNT = 'avis.likes_count';

    public const AVIS_SIGNALEMENTS_COUNT = 'avis.signalements_count';

    // Timestamps
    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const DELETED_AT = 'deleted_at';

    /**
     * Structure complète d'un avis dans le tableau
     */
    public static function avisStructure(array $data): array
    {
        return [
            'review_id'          => $data['review_id'],
            'user_id'            => $data['user_id'],
            'user_prenom'        => $data['user_prenom'],
            'note'               => (int) $data['note'],
            'commentaire'        => $data['commentaire'],
            'date_creation'      => $data['date_creation'],
            'statut_moderation'  => $data['statut_moderation'],
            'likes_count'        => (int) ($data['likes_count'] ?? 0),
            'signalements_count' => (int) ($data['signalements_count'] ?? 0),
        ];
    }

    /**
     * Structure pour la distribution des notes
     */
    public static function distributionNotesStructure(): array
    {
        return [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
        ];
    }

    /**
     * Valeurs de statut de modération valides
     */
    public static function getStatutsModeration(): array
    {
        return \App\Domain\Reviews\Enums\ReviewStatusEnum::values();
    }

    /**
     * Structure complète du document
     */
    public static function documentStructure(array $data): array
    {
        return [
            self::FILM_ID            => $data['film_id'],
            self::TITRE_FILM         => $data['titre_film'],
            self::TOTAL_REVIEWS      => (int) ($data['total_reviews'] ?? 0),
            self::NOTE_MOYENNE       => (float) ($data['note_moyenne'] ?? 0.0),
            self::DISTRIBUTION_NOTES => $data['distribution_notes'] ?? self::distributionNotesStructure(),
            self::AVIS               => $data['avis'] ?? [],
        ];
    }
}
