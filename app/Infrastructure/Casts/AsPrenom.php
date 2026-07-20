<?php

declare(strict_types=1);

namespace App\Infrastructure\Casts;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\ValueObjects\Prenom;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Cast pour Prenom Value Object vers/depuis string
 *
 * @implements CastsAttributes<Prenom|null, Prenom|string|null>
 */
final class AsPrenom implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Prenom
    {
        return Prenom::tryFromString($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Prenom) {
            return $value->toString();
        }

        return Prenom::fromString($value)->toString();
    }
}
