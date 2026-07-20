<?php

declare(strict_types=1);

namespace App\Http\Contracts;

use Illuminate\Http\Request;
use App\Application\Contracts\QueryInterface;
use App\Application\Contracts\CommandInterface;

/**
 * Interface pour les mappers de Request HTTP vers Commands/Queries
 *
 * Fournit un contrat pour convertir les données HTTP en objets métier
 */
interface RequestMapperInterface
{
    /**
     * Convertit une Request en Command
     */
    public static function toCommand(Request $request): CommandInterface;

    /**
     * Convertit une Request en Query
     */
    public static function toQuery(Request $request): QueryInterface;
}
