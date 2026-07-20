<?php

declare(strict_types=1);

namespace App\Infrastructure\Casts;

use Exception;
use Money\Money;
use Money\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Cast pour Money (MoneyPHP) vers/depuis JSON
 * Stockage JSON : {"amount": "1250", "currency": "EUR"}
 *
 * @implements CastsAttributes<Money|null, Money|string|array<string, mixed>|null>
 */
final class AsMoney implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Money
    {
        if ($value === null) {
            return null;
        }

        $data = is_string($value) ? json_decode($value, true) : $value;

        if (!is_array($data) || !isset($data['amount'], $data['currency'])) {
            return null;
        }

        try {
            return new Money($data['amount'], new Currency($data['currency']));
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Money) {
            $result = json_encode([
                'amount'   => $value->getAmount(),
                'currency' => $value->getCurrency()->getCode(),
            ]);

            return $result ?: null;
        }

        // Handle array format - PHPStan knows $value is not Money or null here
        /** @var array<string, mixed> $arrayValue */
        $arrayValue = (array) $value;
        if (is_array($value) && isset($arrayValue['amount'], $arrayValue['currency'])) {
            try {
                $money = new Money($arrayValue['amount'], new Currency($arrayValue['currency']));

                $result = json_encode([
                    'amount'   => $money->getAmount(),
                    'currency' => $money->getCurrency()->getCode(),
                ]);

                return $result ?: null;
            } catch (Exception) {
                return null;
            }
        }

        return null;
    }
}
