<?php

declare(strict_types=1);

namespace App\Infrastructure\Casts;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\ValueObjects\Email;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Cast automatique Laravel pour Email Value Object
 *
 * @implements CastsAttributes<Email, string>
 */
final class EmailCast implements CastsAttributes
{
    /**
     * Cast la valeur récupérée depuis la base vers un VO Email
     *
     * @param array<string, mixed> $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Email
    {
        return Email::tryFromString($value);
    }

    /**
     * Prépare la valeur pour stockage en base
     *
     * @param array<string, mixed> $attributes
     * @param Email|string|null $value
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value instanceof Email) {
            return $value->value;
        }

        if (is_string($value)) {
            return Email::fromString($value)->value;
        }

        return $value;
    }
}
