<?php

declare(strict_types=1);

namespace App\Infrastructure\Casts;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\ValueObjects\Devise;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Cast pour Devise Value Object vers/depuis string
 *
 * @implements CastsAttributes<Devise|null, Devise|string|null>
 */
final class AsDevise implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Devise
    {
        return Devise::tryFromString($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Devise) {
            return $value->getCode();
        }

        return Devise::fromString($value)->getCode();
    }
}
