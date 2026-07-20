<?php

declare(strict_types=1);

namespace App\Application\Public\Cinema\Queries\GetPublicCinemaDetail;

use Exception;
use App\Application\Contracts\Result;
use App\Application\Contracts\QueryInterface;
use App\Application\Cinema\DTOs\CinemaDetailDto;
use App\Application\Contracts\QueryHandlerInterface;
use App\Infrastructure\Database\ReadModels\CinemaPublic;

/**
 * Handler MongoDB pour le détail d'un cinéma public
 * Utilise MongoDB pour des performances optimales en lecture (read-side CQRS)
 */
final class GetPublicCinemaDetailQueryHandler implements QueryHandlerInterface
{
    public function handle(QueryInterface $query): Result
    {
        assert($query instanceof GetPublicCinemaDetailQuery);

        try {
            // Utiliser le modèle MongoDB pour la lecture optimisée
            $cinema = CinemaPublic::where('cinema_id', $query->cinemaUuid)
                ->actif() // Scope pour cinémas actifs seulement
                ->first();

            if (!$cinema) {
                return Result::error(
                    'CINEMA_NOT_FOUND',
                    'Cinéma non trouvé'
                );
            }

            // Mapper vers DTO
            $cinemaDto = $this->mapToDto($cinema);

            return Result::success(
                new GetPublicCinemaDetailQueryResponse($cinemaDto)
            );

        } catch (Exception $e) {
            return Result::error(
                'PUBLIC_CINEMA_QUERY_FAILED',
                'Erreur lors de la récupération du cinéma depuis MongoDB: ' . $e->getMessage()
            );
        }
    }

    /**
     * Mapper le modèle MongoDB vers DTO
     */
    private function mapToDto(CinemaPublic $cinema): CinemaDetailDto
    {
        // Mapper les salles depuis MongoDB (déjà dénormalisées) - FILTRER ACTIVES SEULEMENT
        $salles = [];
        // $cinema->salles is always array due to cast in model
        // Filtrer seulement les salles actives pour le front public
        /** @var array<array<string, mixed>> */
        $sallesActives = array_filter($cinema->salles, function (mixed $salle): bool {
            if (!is_array($salle)) {
                return false;
            }

            return ($salle['statut'] ?? 'ACTIVE') === 'ACTIVE';
        });

        $salles = array_map(function (mixed $salle) use ($cinema): object {

            // Utiliser directement les champs MongoDB
            $qualiteProjection = $salle['qualite_projection'] ?? [];
            $qualiteSonore     = $salle['qualite_sonore'] ?? [];

            // Retourner un objet pour que Blade puisse utiliser la syntaxe ->
            return (object) [
                'uuid'              => $salle['salle_id'] ?? '',
                'nom'               => $salle['nom'] ?? '',
                'capaciteTotale'    => $salle['capacite_totale'] ?? 0,
                'capacitePmr'       => $salle['capacite_pmr'] ?? 0,
                'capaciteStandard'  => max(0, $this->safeCastToInt($salle['capacite'] ?? 0) - $this->safeCastToInt($salle['capacite_pmr'] ?? 0)),
                'qualiteProjection' => $qualiteProjection,
                'qualiteSonore'     => $qualiteSonore,
                'accessibilitePmr'  => $salle['accessibilite_pmr'] ?? false,
                'climatisation'     => $salle['climatisation'] ?? false,
                'statut'            => $salle['statut'] ?? 'ACTIVE',
                'estDisponible'     => ($salle['statut'] ?? 'ACTIVE') === 'ACTIVE',
                'cinemaNom'         => $cinema->nom ?? '',
            ];
        }, $sallesActives);

        // Services are always array due to cast in model - convert to string array
        $services = array_filter(
            array_map(
                static fn (mixed $value): string => is_scalar($value) ? (string) $value : '',
                array_values($cinema->services)
            ),
            static fn (string $value): bool => $value !== ''
        );

        // Horaires are always array due to cast in model
        $horairesArray = $cinema->horaires_ouverture;

        return new CinemaDetailDto(
            uuid: $cinema->cinema_id ?? '',
            nom: $cinema->nom,
            pays: $cinema->pays ?? 'FR',
            adresse: $cinema->adresse,
            ville: $cinema->ville,
            codePostal: $cinema->code_postal,
            telephone: $this->extractTelephone($cinema),
            email: $this->extractEmail($cinema),
            description: $cinema->description,
            estActif: $cinema->statut === 'actif',
            latitude: $cinema->latitude,
            longitude: $cinema->longitude,
            nombreSalles: $cinema->nombre_salles,
            horairesOuverture: \App\Domain\Shared\ValueObjects\HorairesOuverture::tryFromArray($horairesArray),
            accessibilitePmr: $this->hasAccessibilityPmr($cinema),
            salles: $salles,
            seancesAVenir: [],
            services: $services,
            acces: [],
            horairesArray: $horairesArray,
            createdAt: $cinema->created_at->toDateTimeImmutable(),
            updatedAt: $cinema->updated_at->toDateTimeImmutable(),
        );
    }

    /**
     * Extrait le téléphone de manière sécurisée
     */
    private function extractTelephone(CinemaPublic $cinema): string
    {
        $telephone = $cinema->telephone;

        if (is_string($telephone)) {
            return $telephone;
        }

        if (!empty($telephone) && array_key_exists(0, $telephone)) {
            $firstElement = $telephone[0];
            if (is_string($firstElement)) {
                return $firstElement;
            }
        }

        return '';
    }

    /**
     * Extrait l'email de manière sécurisée
     */
    private function extractEmail(CinemaPublic $cinema): string
    {
        $email = $cinema->email;

        if (is_string($email)) {
            return $email;
        }

        // At this point, $email must be an array since we checked is_string above
        if (isset($email['value'])) {
            // Structure: ['value' => ['value' => 'email@example.com']]
            if (is_array($email['value']) && isset($email['value']['value'])) {
                return is_scalar($email['value']['value']) ? (string) $email['value']['value'] : '';
            }
            // Structure: ['value' => 'email@example.com']
            if (is_string($email['value'])) {
                return $email['value'];
            }
        }
        // Structure: ['email@example.com']
        if (array_key_exists(0, $email) && is_string($email[0])) {
            return $email[0];
        }

        return '';
    }

    /**
     * Vérifie si le cinéma a une accessibilité PMR
     */
    private function hasAccessibilityPmr(CinemaPublic $cinema): bool
    {
        // $cinema->salles is always array due to cast in model
        foreach ($cinema->salles as $salle) {
            /** @var array<string, mixed> $salle */
            if (isset($salle['accessibilite_pmr']) && $salle['accessibilite_pmr'] === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Safely cast mixed value to int
     */
    private function safeCastToInt(mixed $value): int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        return 0;
    }
}
