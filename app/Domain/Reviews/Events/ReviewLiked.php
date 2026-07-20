<?php

declare(strict_types=1);

namespace App\Domain\Reviews\Events;

use App\Domain\User\ValueObjects\UserId;
use App\Domain\Shared\Events\DomainEvent;
use App\Domain\Reviews\ValueObjects\ReviewId;

/**
 * Événement déclenché lors du like d'un avis
 * (ReviewLiked dans la doc MongoDB pour alimenter film_reviews)
 */
final class ReviewLiked extends DomainEvent
{
    private function __construct(
        private readonly ReviewId $reviewId,
        private readonly UserId $userId,
        private readonly int $newLikesCount
    ) {
        parent::__construct();
    }

    public static function create(
        ReviewId $reviewId,
        UserId $userId,
        int $newLikesCount
    ): self {
        return new self($reviewId, $userId, $newLikesCount);
    }

    public function getEventName(): string
    {
        return 'reviews.review.liked';
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
            'review_id'       => $this->reviewId->value,
            'user_id'         => $this->userId->value,
            'new_likes_count' => $this->newLikesCount,
        ];
    }

    public function getReviewId(): ReviewId
    {
        return $this->reviewId;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getNewLikesCount(): int
    {
        return $this->newLikesCount;
    }
}
