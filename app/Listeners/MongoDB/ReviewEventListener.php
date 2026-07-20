<?php

declare(strict_types=1);

namespace App\Listeners\MongoDB;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use App\Domain\Reviews\Events\ReviewLiked;
use App\Domain\Reviews\Events\ReviewCreated;
use App\Domain\Reviews\Events\ReviewSignaled;
use App\Domain\Reviews\Events\ReviewModerated;
use App\Infrastructure\Database\ReadModels\FilmReview;
use App\Infrastructure\Schemas\MongoDB\FilmReviewSchema;
use App\Infrastructure\Database\ReadModels\FilmCatalogue;
use App\Infrastructure\Schemas\MongoDB\FilmCatalogueSchema;

/**
 * Listener pour synchroniser les événements Review vers MongoDB
 */
class ReviewEventListener
{
    /**
     * Enregistre les listeners d'événements Review
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            'reviews.review.created',
            [self::class, 'handleReviewCreated']
        );

        $events->listen(
            'reviews.review.moderated',
            [self::class, 'handleReviewModerated']
        );

        $events->listen(
            'reviews.review.liked',
            [self::class, 'handleReviewLiked']
        );

        $events->listen(
            'reviews.review.signaled',
            [self::class, 'handleReviewSignaled']
        );
    }

    /**
     * Gère la création d'un avis
     */
    public function handleReviewCreated(ReviewCreated $event): void
    {
        $review = $event->getReview();
        $filmId = $review->filmId->value;

        // Récupérer ou créer le document film_reviews
        $filmReview = FilmReview::where(FilmReviewSchema::FILM_ID, $filmId)->first();

        if (!$filmReview) {
            $documentData = FilmReviewSchema::documentStructure([
                FilmReviewSchema::FILM_ID            => $filmId,
                FilmReviewSchema::TITRE_FILM         => $event->getTitreFilm() ?? 'Film sans titre',
                FilmReviewSchema::TOTAL_REVIEWS      => 0,
                FilmReviewSchema::NOTE_MOYENNE       => 0.0,
                FilmReviewSchema::DISTRIBUTION_NOTES => FilmReviewSchema::distributionNotesStructure(),
                FilmReviewSchema::AVIS               => [],
            ]);

            $filmReview = FilmReview::create($documentData);
        }

        // Ajouter le nouvel avis
        $avis = $filmReview->{FilmReviewSchema::AVIS} ?? [];

        $nouvelAvis = FilmReviewSchema::avisStructure([
            'review_id'          => $review->id->value,
            'user_id'            => $review->userId->value,
            'user_prenom'        => $review->userPrenom,
            'note'               => $review->note,
            'commentaire'        => $review->commentaire,
            'date_creation'      => $review->dateCreation,
            'statut_moderation'  => $review->statutModeration,
            'likes_count'        => 0,
            'signalements_count' => 0,
        ]);

        $avis[] = $nouvelAvis;

        $updateData = [
            FilmReviewSchema::AVIS       => $avis,
            FilmReviewSchema::UPDATED_AT => now(),
        ];

        $filmReview->update($updateData);

        // Recalculer les statistiques si l'avis est approuvé
        if ($review->statutModeration === 'approved') {
            $this->recalculateFilmStats($filmId);
        }
    }

    /**
     * Gère la modération d'un avis
     */
    public function handleReviewModerated(ReviewModerated $event): void
    {
        $filmId   = $event->getFilmId()->value;
        $reviewId = $event->getReviewId()->value;

        $filmReview = FilmReview::where(FilmReviewSchema::FILM_ID, $filmId)->first();

        if ($filmReview) {
            $avis = collect($filmReview->{FilmReviewSchema::AVIS} ?? [])
                ->map(function ($avis) use ($reviewId, $event) {
                    if ($avis['review_id'] === $reviewId) {
                        $avis['statut_moderation'] = $event->getNewStatus();
                    }

                    return $avis;
                })
                ->toArray();

            $updateData = [
                FilmReviewSchema::AVIS       => $avis,
                FilmReviewSchema::UPDATED_AT => now(),
            ];

            $filmReview->update($updateData);

            // Recalculer les statistiques
            $this->recalculateFilmStats($filmId);
        }
    }

    /**
     * Gère le like d'un avis
     */
    public function handleReviewLiked(ReviewLiked $event): void
    {
        $filmId = $this->getFilmIdByReview($event->getReviewId()->value);

        if ($filmId) {
            $filmReview = FilmReview::where(FilmReviewSchema::FILM_ID, $filmId)->first();

            if ($filmReview) {
                $avis = collect($filmReview->{FilmReviewSchema::AVIS} ?? [])
                    ->map(function ($avis) use ($event) {
                        if ($avis['review_id'] === $event->getReviewId()->value) {
                            $avis['likes_count'] = $event->getNewLikesCount();
                        }

                        return $avis;
                    })
                    ->toArray();

                $updateData = [
                    FilmReviewSchema::AVIS       => $avis,
                    FilmReviewSchema::UPDATED_AT => now(),
                ];

                $filmReview->update($updateData);
            }
        }
    }

    /**
     * Gère le signalement d'un avis
     */
    public function handleReviewSignaled(ReviewSignaled $event): void
    {
        $filmId = $this->getFilmIdByReview($event->getReviewId()->value);

        if ($filmId) {
            $filmReview = FilmReview::where(FilmReviewSchema::FILM_ID, $filmId)->first();

            if ($filmReview) {
                $avis = collect($filmReview->{FilmReviewSchema::AVIS} ?? [])
                    ->map(function ($avis) use ($event) {
                        if ($avis['review_id'] === $event->getReviewId()->value) {
                            $avis['signalements_count'] = $event->getNewSignalementsCount();
                        }

                        return $avis;
                    })
                    ->toArray();

                $updateData = [
                    FilmReviewSchema::AVIS       => $avis,
                    FilmReviewSchema::UPDATED_AT => now(),
                ];

                $filmReview->update($updateData);
            }
        }
    }

    /**
     * Recalcule les statistiques d'un film
     */
    private function recalculateFilmStats(string $filmId): void
    {
        $filmReview = FilmReview::where(FilmReviewSchema::FILM_ID, $filmId)->first();

        if (!$filmReview) {
            return;
        }

        $avisApprouves = collect($filmReview->{FilmReviewSchema::AVIS} ?? [])
            ->where('statut_moderation', 'approved');

        $totalReviews = $avisApprouves->count();
        $noteMoyenne  = $totalReviews > 0 ? $avisApprouves->avg('note') : 0.0;

        // Distribution des notes
        $distribution = FilmReviewSchema::distributionNotesStructure();
        $avisApprouves->groupBy('note')->each(function ($group, $note) use (&$distribution) {
            $distribution[(int) $note] = $group->count();
        });

        $updateData = [
            FilmReviewSchema::TOTAL_REVIEWS      => $totalReviews,
            FilmReviewSchema::NOTE_MOYENNE       => round($noteMoyenne, 1),
            FilmReviewSchema::DISTRIBUTION_NOTES => $distribution,
            FilmReviewSchema::UPDATED_AT         => now(),
        ];

        $filmReview->update($updateData);

        // Mettre à jour le catalogue
        $catalogueUpdateData = [
            FilmCatalogueSchema::NOTE_MOYENNE => round($noteMoyenne, 1),
            FilmCatalogueSchema::NOMBRE_AVIS  => $totalReviews,
            FilmCatalogueSchema::UPDATED_AT   => now(),
        ];

        FilmCatalogue::where(FilmCatalogueSchema::FILM_ID, $filmId)
            ->update($catalogueUpdateData);
    }

    /**
     * Récupère l'ID du film à partir d'un avis
     */
    private function getFilmIdByReview(string $reviewId): ?string
    {
        $review = DB::connection('pgsql')->table('reviews')
            ->where('id', $reviewId)
            ->first(['film_id']);

        return $review?->film_id;
    }
}
