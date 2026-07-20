<?php

declare(strict_types=1);
use App\Domain\Reviews\ValueObjects\ReviewId;

describe('ReviewId - Coverage', function () {
    it('can generate ID', function () {
        $reviewId = ReviewId::generate();
        expect($reviewId->value)->toBeString();
        expect(strlen($reviewId->value))->toBeGreaterThan(10);
    });
    it('can create from string', function () {
        $uuid     = '550e8400-e29b-41d4-a716-446655440000';
        $reviewId = ReviewId::fromString($uuid);
        expect($reviewId->value)->toBe($uuid);
    });
});
