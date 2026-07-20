<?php

declare(strict_types=1);

namespace App\Infrastructure\Casts;

use App\Domain\Shared\ValueObjects\Url;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Cast pour Url Value Object vers/depuis string
 *
 * @implements CastsAttributes<Url|null, Url|string|null>
 */
final class AsUrl implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Url
    {
        return Url::tryFromString($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Url) {
            return $value->toString();
        }

        return Url::fromString($value)->toString();
    }
}
