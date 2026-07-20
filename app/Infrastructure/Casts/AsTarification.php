<?php

declare(strict_types=1);

namespace App\Infrastructure\Casts;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Cinema\ValueObjects\Tarification;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Cast pour Tarification Value Object vers/depuis JSON
 * Stockage JSON : {"tarifs_base": {"normal": 1250, "reduit": 950}, "supplements_speciaux": {...}, "reductions_speciales": {...}}
 *
 * @implements CastsAttributes<Tarification|null, Tarification|string|array<string, mixed>|null>
 */
final class AsTarification implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Tarification
    {
        if ($value === null) {
            return null;
        }

        $data = is_string($value) ? json_decode($value, true) : $value;

        if (!is_array($data)) {
            return null;
        }

        return Tarification::tryFromArray($data);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Tarification) {
            $result = json_encode($value->toArray());

            return $result ?: null;
        }

        if (is_array($value)) {
            /** @var array<string, mixed> $arrayValue */
            $arrayValue   = $value;
            $tarification = Tarification::tryFromArray($arrayValue);

            if ($tarification) {
                $result = json_encode($tarification->toArray());

                return $result ?: null;
            }

            return null;
        }

        return null;
    }
}
