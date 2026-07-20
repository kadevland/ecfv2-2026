<?php

declare(strict_types=1);

namespace App\Infrastructure\Casts;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\ValueObjects\HorairesOuverture;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Cast pour HorairesOuverture Value Object vers/depuis JSONB PostgreSQL
 *
 * @implements CastsAttributes<HorairesOuverture|null, HorairesOuverture|array<string, mixed>|null>
 */
final class AsHorairesOuverture implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?HorairesOuverture
    {
        if ($value === null) {
            return null;
        }

        if ($data = json_decode($value, true)) {
            // Utiliser fromDbData pour éviter la validation stricte lors de la désérialisation
            return HorairesOuverture::fromDbData($data);
        }

        return null;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof HorairesOuverture) {
            $result = json_encode($value->toArray());

            return $result ?: null;
        }

        // PHPStan knows $value is not HorairesOuverture or null here, so cast to array
        try {
            $result = json_encode(HorairesOuverture::fromArray((array) $value)->toArray());

            return $result ?: null;
        } catch (InvalidArgumentException) {
            return null;
        }
    }
}
