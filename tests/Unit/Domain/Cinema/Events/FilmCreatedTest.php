<?php

declare(strict_types=1);

use App\Domain\Cinema\Events\FilmCreated;
use App\Domain\Cinema\ValueObjects\FilmId;

describe('FilmCreated Event', function () {

    it('can be created with film ID', function () {
        $filmId = FilmId::generate();
        $event  = new FilmCreated($filmId);

        expect($event)->toBeInstanceOf(FilmCreated::class);
        expect($event->filmId)->toBe($filmId);
    });

    it('has occurred at timestamp', function () {
        $filmId = FilmId::generate();
        $event  = new FilmCreated($filmId);

        expect($event->occurredAt)->toBeInstanceOf(DateTimeImmutable::class);
    });
});
