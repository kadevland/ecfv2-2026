<?php

declare(strict_types=1);

namespace App\Infrastructure\Casts;

use App\Domain\Shared\ValueObjects\PhoneNumber;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Cast Eloquent pour PhoneNumber Value Object
 * Stockage string E164 : "+33612345678"
 *
 * @implements CastsAttributes<PhoneNumber|null, string|PhoneNumber|null>
 */
class AsPhoneNumber implements CastsAttributes
{
    /**
     * Hydration : string E164 → PhoneNumber VO
     *
     * @param object $model
     * @param mixed $value Valeur E164 depuis la base
     * @param array<string, mixed> $attributes
     */
    public function get($model, string $key, $value, array $attributes): ?PhoneNumber
    {
        return PhoneNumber::tryFromE164($value);
    }

    /**
     * Persistence : PhoneNumber VO → string E164
     *
     * @param object $model
     * @param mixed $value PhoneNumber VO ou string
     * @param array<string, mixed> $attributes
     */
    public function set($model, string $key, $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof PhoneNumber) {
            return $value->telephoneE164;
        }

        return PhoneNumber::fromInternationalFormat($value)->telephoneE164;
    }
}
