<?php

declare(strict_types=1);

namespace App\Http\Mappers\Cinema;

use App\Http\Mappers\BaseRequestMapper;
use App\Domain\Shared\ValueObjects\HorairesOuverture;
use App\Application\Cinema\Commands\CreateCinema\CreateCinemaCommand;

/**
 * Mapper pour convertir les données de requête HTTP en CreateCinemaCommand
 */
final class CreateCinemaRequestMapper extends BaseRequestMapper
{
    /**
     * Convertit les données de requête HTTP en Command
     *
     * @param array<string, mixed> $data
     */
    public static function toCommand(array $data): CreateCinemaCommand
    {
        $horaires = null;
        if (isset($data['horaires_ouverture']) && is_array($data['horaires_ouverture'])) {
            $horaires = HorairesOuverture::fromArray($data['horaires_ouverture']);
        }

        return new CreateCinemaCommand(
            nom: self::sanitizeString($data['nom']) ?? '',
            pays: self::sanitizeString($data['pays']) ?? 'FR',
            rue: self::sanitizeString($data['rue']) ?? '',
            ville: self::sanitizeString($data['ville']) ?? '',
            codePostal: self::sanitizeString($data['code_postal']) ?? '',
            telephone: self::sanitizePhone($data['telephone'] ?? null),
            email: self::validateEmail($data['email'] ?? null),
            description: self::sanitizeString($data['description']),
            estActif: self::toBool($data['est_actif'] ?? true),
            latitude: isset($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float) $data['longitude'] : null,
            horaires: $horaires,
        );
    }
}
