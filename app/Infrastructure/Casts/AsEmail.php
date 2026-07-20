<?php

declare(strict_types=1);

namespace App\Infrastructure\Casts;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\ValueObjects\Email;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Cast pour Email Value Object vers/depuis string
 *
 * @implements CastsAttributes<Email|null, Email|string|null>
 */
final class AsEmail implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Email
    {
        return Email::tryFromString($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Email) {
            return $value->toString();
        }

        return Email::fromString($value)->toString();
    }
}
