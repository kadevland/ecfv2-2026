<?php

declare(strict_types=1);

namespace App\Domain\Reviews\Events;

use App\Domain\User\ValueObjects\UserId;
use App\Domain\Shared\Events\DomainEvent;
use App\Domain\Reviews\ValueObjects\ReviewId;

/**
 * Événement déclenché lors du signalement d'un avis
 * (ReviewSignaled dans la doc MongoDB pour alimenter film_reviews)
 */
final class ReviewSignaled extends DomainEvent
{
    private function __construct(
        private readonly ReviewId $reviewId,
        private readonly UserId $userId,
        private readonly string $motifSignalement,
        private readonly int $newSignalementsCount
    ) {
        parent::__construct();
    }

    public static function create(
        ReviewId $reviewId,
        UserId $userId,
        string $motifSignalement,
        int $newSignalementsCount
    ): self {
        return new self($reviewId, $userId, $motifSignalement, $newSignalementsCount);
    }

    public function getEventName(): string
    {
        return 'reviews.review.signaled';
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
            'review_id'              => $this->reviewId->value,
            'user_id'                => $this->userId->value,
            'motif_signalement'      => $this->motifSignalement,
            'new_signalements_count' => $this->newSignalementsCount,
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

    public function getMotifSignalement(): string
    {
        return $this->motifSignalement;
    }

    public function getNewSignalementsCount(): int
    {
        return $this->newSignalementsCount;
    }
}
