<?php

declare(strict_types=1);

namespace App\Infrastructure\Casts;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\ValueObjects\Prix;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Cast pour Prix Value Object vers/depuis JSON
 * Stockage JSON : {"amount": 1250, "currency": "EUR", "tva_bp": 2000}
 *
 * @implements CastsAttributes<Prix|null, Prix|string|array<string, mixed>|null>
 */
final class AsPrix implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Prix
    {
        if ($value === null) {
            return null;
        }

        $data = is_string($value) ? json_decode($value, true) : $value;

        if (!is_array($data) || !isset($data['amount'], $data['currency'], $data['tva_bp'])) {
            return null;
        }

        return Prix::tryFromArray($data);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Prix) {
            $result = json_encode($value->toArray());

            return $result ?: null;
        }

        if (is_array($value)) {
            /** @var array<string, mixed> $arrayValue */
            $arrayValue = $value;
            $prix       = Prix::tryFromArray($arrayValue);

            if ($prix) {
                $result = json_encode($prix->toArray());

                return $result ?: null;
            }

            return null;
        }

        return null;
    }
}
