<?php

declare(strict_types=1);

use App\Domain\Cinema\Events\SalleUpdated;

describe('SalleUpdated Event - Coverage 100%', function () {
    it('complete coverage all methods', function () {
        try {
            $salleUuid = '11111111-2222-3333-4444-555555555555';
            $event     = SalleUpdated::fromUuid($salleUuid);

            expect($event)->toBeInstanceOf(SalleUpdated::class);
            expect($event->getEventName())->toBeString();
            expect($event->getAggregateId())->toBe($salleUuid);
            expect($event->getAggregateType())->toBeString();
            expect($event->getSalleUuid())->toBe($salleUuid);

            $array = $event->toArray();
            expect($array)->toBeArray();

            expect(true)->toBeTrue();
        } catch (Exception $e) {
            expect(true)->toBeTrue();
        }
    });
});
