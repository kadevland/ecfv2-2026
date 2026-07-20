<?php

declare(strict_types=1);

namespace App\Infrastructure\Casts;

use App\Domain\Shared\ValueObjects\Nom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Cast pour Nom Value Object vers/depuis string
 *
 * @implements CastsAttributes<Nom|null, Nom|string|null>
 */
final class AsNom implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Nom
    {
        return Nom::tryFromString($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Nom) {
            return $value->toString();
        }

        return Nom::fromString($value)->toString();
    }
}
