<?php

declare(strict_types=1);

use App\Domain\User\ValueObjects\UserProfilId;

describe('UserProfilId ValueObject', function () {

    it('génère un ID unique', function () {
        $userProfilId = UserProfilId::generate();

        expect($userProfilId->value)->toBeString();
        expect(strlen($userProfilId->value))->toBeGreaterThan(10);
    });

    it('crée depuis un string', function () {
        $uuid         = 'user-profil-uuid-123';
        $userProfilId = UserProfilId::fromString($uuid);

        expect($userProfilId->value)->toBe($uuid);
        expect((string) $userProfilId)->toBe($uuid);
    });

    it('compare deux IDs identiques', function () {
        $uuid = 'user-profil-uuid-123';
        $id1  = UserProfilId::fromString($uuid);
        $id2  = UserProfilId::fromString($uuid);

        expect($id1->equals($id2))->toBeTrue();
    });

    it('compare deux IDs différents', function () {
        $id1 = UserProfilId::fromString('user-1');
        $id2 = UserProfilId::fromString('user-2');

        expect($id1->equals($id2))->toBeFalse();
    });
});
