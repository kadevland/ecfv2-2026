<?php

declare(strict_types=1);

namespace App\Domain\Reviews\Events;

use App\Domain\Shared\Events\DomainEvent;
use App\Domain\Cinema\ValueObjects\FilmId;
use App\Domain\Reviews\ValueObjects\ReviewId;

/**
 * Événement déclenché lors de la modération d'un avis
 * (ReviewModerated dans la doc MongoDB pour alimenter film_reviews)
 */
final class ReviewModerated extends DomainEvent
{
    private function __construct(
        private readonly ReviewId $reviewId,
        private readonly FilmId $filmId,
        private readonly string $oldStatus,
        private readonly string $newStatus
    ) {
        parent::__construct();
    }

    public static function create(
        ReviewId $reviewId,
        FilmId $filmId,
        string $oldStatus,
        string $newStatus
    ): self {
        return new self($reviewId, $filmId, $oldStatus, $newStatus);
    }

    public function getEventName(): string
    {
        return 'reviews.review.moderated';
    }

    public function getAggregateId(): string
    {
        return $this->reviewId->value;
    }

    public function getAggregateType(): string
    {
        return 'review';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'review_id'  => $this->reviewId->value,
            'film_id'    => $this->filmId->value,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
        ];
    }

    public function getReviewId(): ReviewId
    {
        return $this->reviewId;
    }

    public function getFilmId(): FilmId
    {
        return $this->filmId;
    }

    public function getOldStatus(): string
    {
        return $this->oldStatus;
    }

    public function getNewStatus(): string
    {
        return $this->newStatus;
    }
}
