<?php

declare(strict_types=1);

namespace App\Infrastructure\Casts;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\ValueObjects\Address;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Cast pour Address Value Object vers/depuis JSONB PostgreSQL
 *
 * @implements CastsAttributes<Address|null, Address|array<string, mixed>|null>
 */
final class AsAddress implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Address
    {
        if ($value === null) {
            return null;
        }

        if ($data = json_decode($value, true)) {
            return Address::tryFromArray($data);
        }

        return null;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Address) {
            $result = json_encode($value->toArray());

            return $result ?: null;
        }

        try {
            // Ensure the array has the proper structure for Address::fromArray
            /** @var array{rue: string, ville: string, code_postal: string, pays: string, complement?: string|null} $addressData */
            $addressData = (array) $value;
            $result      = json_encode(Address::fromArray($addressData)->toArray());

            return $result ?: null;
        } catch (InvalidArgumentException) {
            return null;
        }
    }
}
