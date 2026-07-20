<?php

declare(strict_types=1);

namespace App\Application\Public\Cinema\Queries\GetPublicCinemasList;

use Exception;
use App\Application\Contracts\Result;
use App\Application\Contracts\QueryInterface;
use App\Application\Cinema\DTOs\CinemaListItemDto;
use App\Application\Contracts\QueryHandlerInterface;
use App\Infrastructure\Database\ReadModels\CinemaPublic;

/**
 * Handler MongoDB pour la liste publique des cinémas
 * Utilise MongoDB pour des performances optimales en lecture
 */
final class GetPublicCinemasListQueryHandler implements QueryHandlerInterface
{
    public function handle(QueryInterface $query): Result
    {
        assert($query instanceof GetPublicCinemasListQuery);

        try {
            // Utiliser le modèle Eloquent MongoDB
            $mongoQuery = CinemaPublic::query()
                ->actif() // Scope pour cinémas actifs seulement
                ->byLocation($query->location); // Scope pour filtrer par location

            // Appliquer les filtres additionnels si présents
            if ($query->filters) {
                foreach ($query->filters as $field => $value) {
                    $mongoQuery->where($field, $value);
                }
            }

            // Utiliser la pagination Laravel standard comme dans l'admin
            $paginator = $mongoQuery
                ->orderBy('nom')
                ->paginate(
                    perPage: $query->perPage,
                    columns: ['*'],
                    pageName: 'page',
                    page: $query->page
                );

            // Transformer les modèles en DTOs (architecture CQRS)
            $paginator->getCollection()->transform(function ($cinema) {
                return $this->mapModelToDto($cinema);
            });

            return Result::success($paginator);

        } catch (Exception $e) {
            return Result::error(
                'PUBLIC_MONGO_QUERY_FAILED',
                'Erreur lors de la récupération publique depuis MongoDB: ' . $e->getMessage()
            );
        }
    }

    /**
     * Mapper le modèle Eloquent MongoDB vers DTO public
     */
    private function mapModelToDto(CinemaPublic $cinema): CinemaListItemDto
    {
        // Gestion sécurisée de l'email (structure complexe)
        $email = null;
        if (is_array($cinema->email)) {
            if (isset($cinema->email['value'])) {
                // Structure: ['value' => ['value' => 'email@example.com']]
                if (is_array($cinema->email['value']) && isset($cinema->email['value']['value'])) {
                    $email = $cinema->email['value']['value'];
                }
                // Structure: ['value' => 'email@example.com']
                elseif (is_string($cinema->email['value'])) {
                    $email = $cinema->email['value'];
                }
            }
            // Structure: ['email@example.com']
            elseif (isset($cinema->email[0]) && is_string($cinema->email[0])) {
                $email = $cinema->email[0];
            }
        } elseif (is_string($cinema->email)) {
            $email = $cinema->email;
        }

        // Gestion sécurisée du téléphone
        $telephone = null;
        if (is_array($cinema->telephone)) {
            if (empty($cinema->telephone)) {
                $telephone = null;
            } else {
                $firstElement = $cinema->telephone[0] ?? null;
                // Si le premier élément est lui-même un array vide, retourner null
                if (is_array($firstElement) && empty($firstElement)) {
                    $telephone = null;
                } elseif (is_string($firstElement)) {
                    $telephone = $firstElement;
                } else {
                    $telephone = null;
                }
            }
        } elseif (is_string($cinema->telephone)) {
            $telephone = $cinema->telephone;
        }

        return new CinemaListItemDto(
            uuid: is_array($cinema->cinema_id) ? ($cinema->cinema_id['value'] ?? '') : $cinema->cinema_id,
            nom: $cinema->nom ?? 'N/A',
            adresse: $cinema->adresse ?? 'N/A',
            ville: $cinema->ville ?? 'N/A',
            codePostal: $cinema->code_postal ?? 'N/A',
            telephone: $telephone,
            email: $email,
            nombreSalles: $cinema->nombre_salles ?? 0,
            horairesOuverture: is_array($cinema->horaires_ouverture) ? $cinema->horaires_ouverture : [],
            accessibilitePmr: $this->hasAccessibilityPmr($cinema),
            latitude: $cinema->latitude ?? null,
            longitude: $cinema->longitude ?? null,
        );
    }

    /**
     * Vérifie si le cinéma a une accessibilité PMR
     */
    private function hasAccessibilityPmr(CinemaPublic $cinema): bool
    {
        if (is_array($cinema->salles)) {
            foreach ($cinema->salles as $salle) {
                if (isset($salle['accessibilite_pmr']) && $salle['accessibilite_pmr'] === true) {
                    return true;
                }
            }
        }

        return false;
    }
}
