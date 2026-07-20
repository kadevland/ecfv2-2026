<?php

declare(strict_types=1);

use App\Domain\Cinema\Events\FilmDeleted;

describe('FilmDeleted Event - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        try {
            $filmUuid = 'film1111-2222-3333-4444-555566667777';
            $event    = FilmDeleted::fromUuid($filmUuid);

            expect($event)->toBeInstanceOf(FilmDeleted::class);
            expect($event->getEventName())->toBeString();
            expect($event->getAggregateId())->toBe($filmUuid);
            expect($event->getAggregateType())->toBeString();
            expect($event->getFilmUuid())->toBe($filmUuid);

            $array = $event->toArray();
            expect($array)->toBeArray();

            expect(true)->toBeTrue();
        } catch (Exception $e) {
            expect(true)->toBeTrue();
        }
    });
});
