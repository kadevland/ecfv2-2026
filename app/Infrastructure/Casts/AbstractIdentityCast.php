<?php

declare(strict_types=1);

namespace App\Infrastructure\Casts;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\ValueObjects\Identity;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Cast abstrait pour tous les types Identity
 *
 * @template T of Identity
 *
 * @implements CastsAttributes<T, string>
 */
abstract class AbstractIdentityCast implements CastsAttributes
{
    /**
     * Cast la valeur récupérée depuis la base vers un VO Identity
     *
     * @param array<string, mixed> $attributes
     * @return T|null
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Identity
    {
        return $value ? $this->fromString($value) : null;
    }

    /**
     * Prépare la valeur pour stockage en base
     *
     * @param array<string, mixed> $attributes
     * @param T|string|null $value
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value instanceof Identity) {
            return $value->value;
        }

        if (is_string($value)) {
            return $this->fromString($value)->value;
        }

        return null;
    }

    /**
     * Méthode abstraite pour créer le VO spécifique depuis une string
     *
     * @return T
     */
    abstract protected function fromString(string $uuid): Identity;
}
