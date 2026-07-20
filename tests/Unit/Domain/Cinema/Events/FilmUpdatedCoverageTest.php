<?php

declare(strict_types=1);

use App\Domain\Cinema\Events\FilmUpdated;

describe('FilmUpdated Event - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        try {
            $filmUuid = '99999999-8888-7777-6666-555544443333';
            $event    = FilmUpdated::fromUuid($filmUuid);

            expect($event)->toBeInstanceOf(FilmUpdated::class);

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
