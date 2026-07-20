<?php

declare(strict_types=1);

namespace App\Infrastructure\Casts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * @implements CastsAttributes<mixed, mixed>
 */
final class AsIdentity implements CastsAttributes
{
    public function __construct(
        protected string $identityClass
    ) {}

    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }

        return new $this->identityClass($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }

        if (is_string($value)) {
            return $value;
        }

        // Si c'est un objet, on peut vérifier ses méthodes
        if (is_object($value)) {
            // Si c'est déjà un Value Object avec une méthode value()
            if (method_exists($value, 'value')) {
                return $value->value();
            }

            // Si c'est déjà un Value Object avec une méthode toString()
            if (method_exists($value, '__toString')) {
                return (string) $value;
            }
        }

        return (string) $value;
    }
}
