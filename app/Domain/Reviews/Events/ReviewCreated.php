<?php

declare(strict_types=1);

namespace App\Domain\Reviews\Events;

use DateTimeImmutable;
use App\Domain\User\ValueObjects\UserId;
use App\Domain\Shared\Events\DomainEvent;
use App\Domain\Cinema\ValueObjects\FilmId;
use App\Domain\Reviews\ValueObjects\ReviewId;

/**
 * Événement déclenché lors de la création d'un avis
 * (ReviewCreated dans la doc MongoDB pour alimenter film_reviews et films_catalogue)
 */
final class ReviewCreated extends DomainEvent
{
    private function __construct(
        private readonly ReviewId $reviewId,
        private readonly FilmId $filmId,
        private readonly UserId $userId,
        private readonly string $userPrenom,
        private readonly int $note,
        private readonly string $commentaire,
        private readonly DateTimeImmutable $dateCreation,
        private readonly string $statutModeration
    ) {
        parent::__construct();
    }

    public static function create(
        ReviewId $reviewId,
        FilmId $filmId,
        UserId $userId,
        string $userPrenom,
        int $note,
        string $commentaire,
        DateTimeImmutable $dateCreation,
        string $statutModeration
    ): self {
        return new self(
            $reviewId,
            $filmId,
            $userId,
            $userPrenom,
            $note,
            $commentaire,
            $dateCreation,
            $statutModeration
        );
    }

    public function getEventName(): string
    {
        return 'reviews.review.created';
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
            'review_id'         => $this->reviewId->value,
            'film_id'           => $this->filmId->value,
            'user_id'           => $this->userId->value,
            'user_prenom'       => $this->userPrenom,
            'note'              => $this->note,
            'commentaire'       => $this->commentaire,
            'date_creation'     => $this->dateCreation->format('Y-m-d H:i:s'),
            'statut_moderation' => $this->statutModeration,
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

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getUserPrenom(): string
    {
        return $this->userPrenom;
    }

    public function getNote(): int
    {
        return $this->note;
    }

    public function getCommentaire(): string
    {
        return $this->commentaire;
    }

    public function getDateCreation(): DateTimeImmutable
    {
        return $this->dateCreation;
    }

    public function getStatutModeration(): string
    {
        return $this->statutModeration;
    }
}
