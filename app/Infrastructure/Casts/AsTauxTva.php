<?php

declare(strict_types=1);

namespace App\Infrastructure\Casts;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\ValueObjects\TauxTva;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Cast pour TauxTva Value Object vers/depuis integer
 * Stockage en basis points (2000 = 20%)
 *
 * @implements CastsAttributes<TauxTva|null, TauxTva|int|null>
 */
final class AsTauxTva implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?TauxTva
    {
        if ($value === null) {
            return null;
        }

        // Handle JSONB format
        if (is_string($value)) {
            $data = json_decode($value, true);
            if (isset($data['pourcentage'])) {
                return TauxTva::fromPercentage($data['pourcentage']);
            }
            if (isset($data['basis_points'])) {
                return TauxTva::fromBasisPoints($data['basis_points']);
            }
        }

        // Handle array format (from JSONB column)
        if (is_array($value)) {
            if (isset($value['pourcentage'])) {
                return TauxTva::fromPercentage($value['pourcentage']);
            }
            if (isset($value['basis_points'])) {
                return TauxTva::fromBasisPoints($value['basis_points']);
            }
        }

        // Handle integer (legacy format)
        if (is_int($value)) {
            return TauxTva::fromBasisPoints($value);
        }

        return null;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof TauxTva) {
            $result = json_encode([
                'pourcentage'  => $value->getPercentage(),
                'basis_points' => $value->getBasisPoints(),
            ]);

            return $result ?: null;
        }

        // PHPStan knows $value is not TauxTva or null here, so cast to int
        $tauxTva = TauxTva::fromBasisPoints((int) $value);
        $result  = json_encode([
            'pourcentage'  => $tauxTva->getPercentage(),
            'basis_points' => $tauxTva->getBasisPoints(),
        ]);

        return $result ?: null;
    }
}
