<?php

declare(strict_types=1);
use App\Domain\Reviews\Events\ReviewCreated;
use App\Domain\Reviews\ValueObjects\ReviewId;

describe('ReviewCreated - Coverage', function () {
    it('can create event', function () {
        $reviewId = ReviewId::generate();
        $event    = new ReviewCreated($reviewId);
        expect($event)->toBeInstanceOf(ReviewCreated::class);
        expect($event->reviewId)->toBe($reviewId);
    });
});
