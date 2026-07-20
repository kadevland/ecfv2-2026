<?php

declare(strict_types=1);

use App\Domain\Cinema\ValueObjects\FilmId;

describe('FilmId ValueObject', function () {

    it('génère un ID unique', function () {
        $filmId = FilmId::generate();

        expect($filmId->value)->toBeString();
        expect(strlen($filmId->value))->toBeGreaterThan(10);
    });

    it('crée depuis un string', function () {
        $uuid   = 'a1b2c3d4-e5f6-4321-8765-123456789abc';
        $filmId = FilmId::fromString($uuid);

        expect($filmId->value)->toBe($uuid);
        expect((string) $filmId)->toBe($uuid);
    });

    it('compare deux IDs identiques', function () {
        $uuid = 'a1b2c3d4-e5f6-4321-8765-123456789abc';
        $id1  = FilmId::fromString($uuid);
        $id2  = FilmId::fromString($uuid);

        expect($id1->equals($id2))->toBeTrue();
    });

    it('compare deux IDs différents', function () {
        $id1 = FilmId::fromString('a1b2c3d4-e5f6-4321-8765-123456789abc');
        $id2 = FilmId::fromString('a2b2c3d4-e5f6-4321-8765-123456789abc');

        expect($id1->equals($id2))->toBeFalse();
    });
});
