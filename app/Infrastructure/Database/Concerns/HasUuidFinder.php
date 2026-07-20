<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait pour recherche par UUID sur les modèles
 */
trait HasUuidFinder
{
    /**
     * Trouve un modèle par son UUID
     */
    public static function findByUuid(string $uuid): ?static
    {
        return static::where(static::getUuidColumnName(), $uuid)->first();
    }

    /**
     * Trouve un modèle par son UUID ou fail
     */
    public static function findByUuidOrFail(string $uuid): static
    {
        return static::where(static::getUuidColumnName(), $uuid)->firstOrFail();
    }

    /**
     * Scope: Where UUID equals
     *
     * @param Builder<static> $query
     * @return Builder<static>
     */
    public function scopeWhereUuid(Builder $query, string $uuid): Builder
    {
        return $query->where(static::getUuidColumnName(), $uuid);
    }

    /**
     * Get the UUID column name for this model
     */
    abstract protected static function getUuidColumnName(): string;
}
