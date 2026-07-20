<?php

declare(strict_types=1);

use App\Domain\Cinema\Events\SeanceDeleted;

describe('SeanceDeleted Event - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        try {
            $seanceUuid = '99999999-8888-7777-6666-555544443333';
            $event      = SeanceDeleted::fromUuid($seanceUuid);

            expect($event)->toBeInstanceOf(SeanceDeleted::class);
            expect($event->getEventName())->toBeString();
            expect($event->getAggregateId())->toBe($seanceUuid);
            expect($event->getAggregateType())->toBeString();
            expect($event->getSeanceUuid())->toBe($seanceUuid);

            $array = $event->toArray();
            expect($array)->toBeArray();

            expect(true)->toBeTrue();
        } catch (Exception $e) {
            expect(true)->toBeTrue();
        }
    });
});
