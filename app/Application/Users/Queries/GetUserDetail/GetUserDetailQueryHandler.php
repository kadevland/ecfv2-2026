<?php

declare(strict_types=1);

namespace App\Application\Users\Queries\GetUserDetail;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Application\Contracts\Result;
use App\Application\Contracts\QueryInterface;
use App\Application\Users\DTOs\UserDetailDto;
use App\Application\Contracts\QueryHandlerInterface;
use App\Application\Users\Queries\GetUserDetailQuery;
use App\Domain\Users\Repositories\UserRepositoryInterface;
use App\Infrastructure\Database\Models\Profiles\UserProfil;
use App\Infrastructure\Database\Schemas\Employees\EmploiSchema;
use App\Infrastructure\Database\Schemas\Employees\ContratSchema;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;

final class GetUserDetailQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function handle(QueryInterface $query): Result
    {
        if (!$query instanceof GetUserDetailQuery) {
            return Result::error(
                'INVALID_QUERY_TYPE',
                'Type de requête invalide'
            );
        }

        try {
            if (!$query->isValid()) {
                return Result::error(
                    'INVALID_QUERY',
                    'Données de requête invalides'
                );
            }

            // Optimisation CQRS: chercher d'abord dans user_profils (plus rapide)
            $profil = UserProfil::where(UserProfilSchema::USER_ID, $query->userUuid)->first();

            if (!$profil) {
                // Fallback: si pas de profil, chercher dans users
                $user = $this->userRepository->findByUuid($query->userUuid);
                if (!$user) {
                    return Result::error(
                        'USER_NOT_FOUND',
                        'Utilisateur non trouvé'
                    );
                }

                $dto = $this->mapToDetailDtoFromUser($user, $query);
            } else {
                // Optimisation: utiliser les données du profil directement
                $dto = $this->mapToDetailDtoFromProfil($profil, $query);
            }

            return Result::success($dto);

        } catch (Exception $e) {
            return Result::error(
                'QUERY_FAILED',
                'Erreur lors de la récupération: ' . $e->getMessage()
            );
        }
    }

    private function mapToDetailDtoFromUser(\App\Infrastructure\Database\Models\Auth\User $user, GetUserDetailQuery $query): UserDetailDto
    {
        // Récupérer les informations professionnelles pour les employés
        $employeeInfo = null;
        if ($user->type->value === 'employee' && $user->profil) {
            $employeeInfo = $this->getEmployeeDetails($user);
        }

        // Map Laravel User model to DTO using relationships
        return new UserDetailDto(
            uuid: (string) $user->id,
            nom: $user->profil->nom ?? '',
            prenom: $user->profil->prenom ?? '',
            email: $user->credential->email ?? '',
            telephone: $user->profil->telephone ?? '',
            type: $user->type->value ?? 'client',
            typeLabel: $this->getTypeLabel($user->type->value ?? 'client'),
            estActif: $user->is_active,
            emailVerified: $user->credential?->email_verified_at !== null,
            dateNaissance: $user->profil?->date_naissance?->format('Y-m-d'),
            sexe: $user->profil->sexe->value ?? null,
            adresse: $user->profil->adresse ?? '',
            ville: $user->profil->ville ?? '',
            codePostal: $user->profil->code_postal ?? '',
            pays: $user->profil->pays ?? 'FR',
            poste: $employeeInfo->titre_poste ?? '',
            departement: $employeeInfo->categorie ?? '',
            salaire: $employeeInfo?->salaire_brut_ht_centimes ? ($employeeInfo->salaire_brut_ht_centimes / 100) : null,
            dateEmbauche: $employeeInfo->date_debut ?? null,
            avatar: $user->profil->avatar ?? '',
            permissions: [],
            roles: [],
            createdAt: $user->created_at->format('Y-m-d H:i:s'),
            updatedAt: $user->updated_at->format('Y-m-d H:i:s'),
        );
    }

    /**
     * Méthode optimisée CQRS: mapping depuis UserProfil directement (sans JOIN)
     */
    private function mapToDetailDtoFromProfil(UserProfil $profil, GetUserDetailQuery $query): UserDetailDto
    {
        // Récupérer les informations professionnelles pour les employés si nécessaire
        $employeeInfo = null;
        if ($profil->type->value === 'employee') {
            // Utiliser user_uuid pour récupérer les infos employé
            $employeeInfo = $this->getEmployeeDetailsByUuid($profil->user_uuid);
        }

        // Récupérer les infos user minimales si nécessaire (is_active, dates)
        $userInfo = $this->getUserMinimalInfo($profil->user_db_id);

        return new UserDetailDto(
            uuid: $profil->user_uuid,
            nom: $profil->nom,
            prenom: $profil->prenom,
            email: $profil->email,
            telephone: $profil->telephone ?? '',
            type: $profil->type->value,
            typeLabel: $this->getTypeLabel($profil->type->value),
            estActif: $userInfo?->is_active ?? true,
            emailVerified: $userInfo?->email_verified_at !== null,
            dateNaissance: $profil->date_naissance?->format('Y-m-d'),
            sexe: $profil->sexe->value ?? null,
            adresse: $profil->adresse['rue'] ?? '',
            ville: $profil->adresse['ville'] ?? '',
            codePostal: $profil->adresse['code_postal'] ?? '',
            pays: $profil->adresse['pays'] ?? 'FR',
            poste: $employeeInfo->titre_poste ?? '',
            departement: $employeeInfo->categorie ?? '',
            salaire: $employeeInfo?->salaire_brut_ht_centimes ? ($employeeInfo->salaire_brut_ht_centimes / 100) : null,
            dateEmbauche: $employeeInfo->date_debut ?? null,
            avatar: '', // À récupérer si nécessaire
            permissions: [],
            roles: [],
            createdAt: $userInfo?->created_at ? \Carbon\Carbon::parse($userInfo->created_at)->format('Y-m-d H:i:s') : null,
            updatedAt: $userInfo?->updated_at ? \Carbon\Carbon::parse($userInfo->updated_at)->format('Y-m-d H:i:s') : null,
        );
    }

    private function getTypeLabel(string $type): string
    {
        return match ($type) {
            'client'   => 'Client',
            'employee' => 'Employé',
            'admin'    => 'Administrateur',
            default    => ucfirst($type),
        };
    }

    /**
     * Récupérer les détails professionnels d'un employé via son contrat actuel
     */
    private function getEmployeeDetails(\App\Infrastructure\Database\Models\Auth\User $user): ?object
    {
        try {
            $contratActuel = DB::table(ContratSchema::FULL_TABLE . ' as c')
                ->join(EmploiSchema::FULL_TABLE . ' as e', 'c.' . ContratSchema::EMPLOI_UUID, '=', 'e.' . EmploiSchema::ID)
                ->where('c.' . ContratSchema::USER_UUID, (string) $user->id)
                ->where('c.' . ContratSchema::STATUT, 'ACTIF')
                ->select(
                    'e.' . EmploiSchema::TITRE_POSTE . ' as titre_poste',
                    'e.' . EmploiSchema::CATEGORIE . ' as categorie',
                    'c.' . ContratSchema::SALAIRE_BRUT_HT_CENTIMES . ' as salaire_brut_ht_centimes',
                    'c.' . ContratSchema::DATE_DEBUT . ' as date_debut',
                    'c.' . ContratSchema::TYPE_CONTRAT . ' as type_contrat',
                    'c.' . ContratSchema::TEMPS_TRAVAIL . ' as temps_travail',
                    'c.' . ContratSchema::HEURES_HEBDOMADAIRES . ' as heures_hebdomadaires'
                )
                ->orderBy('c.' . ContratSchema::DATE_DEBUT, 'desc')
                ->first();

            return $contratActuel;

        } catch (Exception $e) {
            // En cas d'erreur, retourner null pour éviter de casser l'affichage
            return null;
        }
    }

    /**
     * Récupérer les informations minimales du user (optimisé pour CQRS)
     */
    private function getUserMinimalInfo(int $userDbId): ?object
    {
        try {
            return DB::table('auth.users')
                ->select('is_active', 'created_at', 'updated_at')
                ->join('auth.user_credentials', 'users.db_id', '=', 'user_credentials.user_db_id')
                ->addSelect('user_credentials.email_verified_at')
                ->where('users.db_id', $userDbId)
                ->first();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Récupérer les détails employé par user_uuid (optimisé)
     */
    private function getEmployeeDetailsByUuid(string $userUuid): ?object
    {
        try {
            $contratActuel = DB::table(ContratSchema::FULL_TABLE . ' as c')
                ->join(EmploiSchema::FULL_TABLE . ' as e', 'c.' . ContratSchema::EMPLOI_UUID, '=', 'e.' . EmploiSchema::ID)
                ->where('c.' . ContratSchema::USER_UUID, $userUuid)
                ->where('c.' . ContratSchema::STATUT, 'ACTIF')
                ->select(
                    'e.' . EmploiSchema::TITRE_POSTE . ' as titre_poste',
                    'e.' . EmploiSchema::CATEGORIE . ' as categorie',
                    'c.' . ContratSchema::SALAIRE_BRUT_HT_CENTIMES . ' as salaire_brut_ht_centimes',
                    'c.' . ContratSchema::DATE_DEBUT . ' as date_debut',
                    'c.' . ContratSchema::TYPE_CONTRAT . ' as type_contrat',
                    'c.' . ContratSchema::TEMPS_TRAVAIL . ' as temps_travail',
                    'c.' . ContratSchema::HEURES_HEBDOMADAIRES . ' as heures_hebdomadaires'
                )
                ->orderBy('c.' . ContratSchema::DATE_DEBUT, 'desc')
                ->first();

            return $contratActuel;

        } catch (Exception $e) {
            // En cas d'erreur, retourner null pour éviter de casser l'affichage
            return null;
        }
    }
}
