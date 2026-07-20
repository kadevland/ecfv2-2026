<?php

declare(strict_types=1);

namespace App\Infrastructure\Casts;

use App\Domain\Shared\Enums\CodePays;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Cast pour CodePays Enum vers/depuis string
 *
 * @implements CastsAttributes<CodePays|null, CodePays|string|null>
 */
final class AsCodePays implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?CodePays
    {
        return CodePays::tryFromCode($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof CodePays) {
            return $value->value;
        }

        // PHPStan knows $value is not CodePays or null here, so it must be string
        $codePaysParsed = CodePays::tryFromCode((string) $value);

        return $codePaysParsed?->value;
    }
}
