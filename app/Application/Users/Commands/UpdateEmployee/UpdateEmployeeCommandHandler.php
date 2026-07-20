<?php

declare(strict_types=1);

namespace App\Application\Users\Commands\UpdateEmployee;

use Exception;
use App\Application\Contracts\Result;
use App\Application\Contracts\CommandInterface;
use App\Application\Contracts\CommandHandlerInterface;
use App\Infrastructure\Database\Models\Auth\UserCredential;
use App\Domain\User\Repositories\UserProfilRepositoryInterface;
use App\Infrastructure\Database\Schemas\Auth\UserCredentialSchema;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;
use App\Infrastructure\Database\Models\Profiles\UserProfil as UserProfilModel;

final readonly class UpdateEmployeeCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        /** @phpstan-ignore-next-line */
        private UserProfilRepositoryInterface $userProfilRepository
    ) {}

    public function handle(CommandInterface $command): Result
    {
        if (!$command instanceof UpdateEmployeeCommand) {
            return Result::error(
                'INVALID_COMMAND_TYPE',
                'Handler expects UpdateEmployeeCommand'
            );
        }

        // Validation de la command
        $validationErrors = $command->validate();
        if (!empty($validationErrors)) {
            return Result::error(
                $validationErrors,
                'Données invalides: ' . implode(', ', $validationErrors)
            );
        }

        try {
            // Chercher directement le modèle profil
            $profilModel = UserProfilModel::where(UserProfilSchema::USER_ID, $command->userUuid)->first();

            if (!$profilModel) {
                return Result::error(
                    'PROFIL_NOT_FOUND',
                    'Profil employé non trouvé'
                );
            }

            // Préparer les données profil
            $profileData = [];

            if ($command->prenom !== null) {
                $profileData[UserProfilSchema::PRENOM] = $command->prenom;
            }

            if ($command->nom !== null) {
                $profileData[UserProfilSchema::NOM] = $command->nom;
            }

            if ($command->telephone !== null) {
                $profileData[UserProfilSchema::TELEPHONE] = $command->telephone;
            }

            if ($command->dateNaissance !== null) {
                $profileData[UserProfilSchema::DATE_NAISSANCE] = $command->dateNaissance;
            }

            if ($command->sexe !== null) {
                $profileData[UserProfilSchema::SEXE] = $command->sexe;
            }

            if ($command->adresse !== null) {
                $profileData[UserProfilSchema::ADRESSE] = $command->adresse;
            }

            // Mettre à jour le profil directement
            $profilUpdated = false;
            if (!empty($profileData)) {
                $profilModel->fill($profileData);
                $profilUpdated = $profilModel->save();
            }

            // Mettre à jour l'email séparément si nécessaire
            $emailUpdated = true;
            if ($command->email !== null) {
                $credential = UserCredential::where(UserCredentialSchema::USER_ID, $profilModel->user_uuid)->first();
                if ($credential) {
                    $credential->email = $command->email;
                    $emailUpdated      = $credential->save();
                }
            }

            // Vérifier le succès
            if (!$profilUpdated && $command->email === null) {
                return Result::error(
                    'NO_UPDATES',
                    'Aucune donnée à mettre à jour'
                );
            }

            if (!$profilUpdated || !$emailUpdated) {
                return Result::error(
                    'UPDATE_FAILED',
                    'Erreur lors de la mise à jour de l\'employé'
                );
            }

            return Result::success($profilModel);

        } catch (Exception $e) {
            return Result::error(
                'UPDATE_FAILED',
                'Erreur lors de la mise à jour de l\'employé: ' . $e->getMessage()
            );
        }
    }
}
