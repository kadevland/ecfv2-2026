<?php

declare(strict_types=1);

namespace App\Infrastructure\Casts;

use App\Domain\Shared\Enums\CodePays;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Shared\ValueObjects\CodePostal;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * Cast pour CodePostal Value Object vers/depuis string
 * Nécessite un attribut pays dans le modèle
 *
 * @implements CastsAttributes<CodePostal|null, CodePostal|string|null>
 */
final class AsCodePostal implements CastsAttributes
{
    private string $paysAttribute;

    public function __construct(string $paysAttribute = 'pays')
    {
        $this->paysAttribute = $paysAttribute;
    }

    public function get(Model $model, string $key, mixed $value, array $attributes): ?CodePostal
    {
        if ($value === null) {
            return null;
        }

        // Récupérer le pays depuis l'attribut spécifié
        $paysValue = $attributes[$this->paysAttribute] ?? null;
        if ($paysValue === null) {
            return null; // Graceful fallback au lieu d'exception
        }

        $pays = is_string($paysValue) ? CodePays::tryFrom($paysValue) : $paysValue;
        if ($pays === null) {
            return null;
        }

        return CodePostal::tryFromString($value, $pays);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof CodePostal) {
            return $value->toString();
        }

        // Pour les strings, on a besoin du pays pour valider
        $paysValue = $attributes[$this->paysAttribute] ?? null;
        if ($paysValue === null) {
            return $value; // Fallback : stockage direct
        }

        $pays = is_string($paysValue) ? CodePays::tryFrom($paysValue) : $paysValue;
        if ($pays === null) {
            return $value;
        }

        $codePostal = CodePostal::tryFromString($value, $pays);

        return $codePostal?->toString() ?? $value;
    }
}
