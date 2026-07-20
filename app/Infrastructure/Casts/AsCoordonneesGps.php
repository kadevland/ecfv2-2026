<?php

declare(strict_types=1);

namespace App\Infrastructure\Casts;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\ValueObjects\CoordonneesGps;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Cast pour CoordonneesGps Value Object vers/depuis JSONB PostgreSQL
 *
 * @implements CastsAttributes<CoordonneesGps|null, CoordonneesGps|array<string, mixed>|null>
 */
final class AsCoordonneesGps implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?CoordonneesGps
    {
        if ($value === null) {
            return null;
        }

        if ($data = json_decode($value, true)) {
            return CoordonneesGps::tryFromArray($data);
        }

        return null;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof CoordonneesGps) {
            $result = json_encode($value->toArray());

            return $result ?: null;
        }

        // PHPStan knows $value is not CoordonneesGps or null here, so try to cast to array
        try {
            /** @var array{latitude: float|string, longitude: float|string} $gpsData */
            $gpsData = (array) $value;
            $result  = json_encode(CoordonneesGps::fromArray($gpsData)->toArray());

            return $result ?: null;
        } catch (InvalidArgumentException) {
            return null;
        }
    }
}
