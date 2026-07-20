<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\ReadModels;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Infrastructure\Database\Schemas\Reviews\FilmReviewSchema;

/**
 * Modèle MongoDB pour la collection film_reviews
 * Collection optimisée pour les requêtes de consultation des avis par film
 */
class FilmReview extends Model
{
    use SoftDeletes;

    public $connection = FilmReviewSchema::CONNECTION;

    protected $collection = FilmReviewSchema::COLLECTION;

    protected $fillable = [
        FilmReviewSchema::FILM_ID,
        FilmReviewSchema::TITRE_FILM,
        FilmReviewSchema::TOTAL_REVIEWS,
        FilmReviewSchema::NOTE_MOYENNE,
        FilmReviewSchema::DISTRIBUTION_NOTES,
        FilmReviewSchema::AVIS,
    ];

    protected $casts = [
        FilmReviewSchema::TOTAL_REVIEWS      => 'integer',
        FilmReviewSchema::NOTE_MOYENNE       => 'float',
        FilmReviewSchema::DISTRIBUTION_NOTES => 'array',
        FilmReviewSchema::AVIS               => 'array',
        FilmReviewSchema::CREATED_AT         => 'datetime',
        FilmReviewSchema::UPDATED_AT         => 'datetime',
        FilmReviewSchema::DELETED_AT         => 'datetime',
    ];

    protected $dates = [
        FilmReviewSchema::CREATED_AT,
        FilmReviewSchema::UPDATED_AT,
        FilmReviewSchema::DELETED_AT,
    ];

    /**
     * Récupère les avis par film avec pagination
     */
    public function scopeByFilm($query, string $filmId)
    {
        return $query->where(FilmReviewSchema::FILM_ID, $filmId);
    }

    /**
     * Récupère les avis modérés (statut approved)
     */
    public function scopeApproved($query)
    {
        return $query->where(FilmReviewSchema::AVIS . '.statut_moderation', 'approved');
    }

    /**
     * Récupère les avis avec note minimum
     */
    public function scopeMinNote($query, int $minNote)
    {
        return $query->where(FilmReviewSchema::AVIS . '.note', '>=', $minNote);
    }

    /**
     * Trie par date de création des avis (plus récents d'abord)
     */
    public function scopeRecent($query)
    {
        return $query->orderBy(FilmReviewSchema::AVIS . '.date_creation', 'desc');
    }
}
