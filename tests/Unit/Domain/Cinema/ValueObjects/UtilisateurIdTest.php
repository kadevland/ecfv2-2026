<?php

declare(strict_types=1);
use App\Domain\Cinema\ValueObjects\UtilisateurId;

describe('UtilisateurId - Coverage', function () {
    it('can generate ID', function () {
        $userId = UtilisateurId::generate();
        expect($userId->value)->toBeString();
        expect(strlen($userId->value))->toBeGreaterThan(10);
    });
    it('can create from string', function () {
        $uuid   = '550e8400-e29b-41d4-a716-446655440000';
        $userId = UtilisateurId::fromString($uuid);
        expect($userId->value)->toBe($uuid);
    });
});
