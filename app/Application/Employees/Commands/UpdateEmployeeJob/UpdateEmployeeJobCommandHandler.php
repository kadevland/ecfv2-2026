<?php

declare(strict_types=1);

namespace App\Application\Employees\Commands\UpdateEmployeeJob;

use Exception;
use App\Application\Contracts\Result;
use App\Application\Contracts\CommandInterface;
use App\Application\Contracts\CommandHandlerInterface;
use App\Infrastructure\Database\Models\Employees\Emploi;
use App\Infrastructure\Database\Models\Profiles\UserProfil;
use App\Infrastructure\Database\Schemas\Employees\EmploiSchema;
use App\Domain\Employees\Repositories\EmploiRepositoryInterface;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;

final readonly class UpdateEmployeeJobCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private EmploiRepositoryInterface $emploiRepository,
    ) {}

    public function handle(CommandInterface $command): Result
    {
        assert($command instanceof UpdateEmployeeJobCommand);

        // Validation des données
        $validationErrors = $command->validate();
        if (!empty($validationErrors)) {
            return Result::error('VALIDATION_FAILED', implode(', ', $validationErrors));
        }

        try {
            // Récupérer le profil utilisateur pour avoir le USER_PROFIL_KEY
            $userProfil = UserProfil::query()
                ->where(UserProfilSchema::USER_ID, $command->userUuid)
                ->first();

            if (!$userProfil) {
                return Result::error('USER_NOT_FOUND', 'Utilisateur non trouvé');
            }

            // Récupérer ou créer l'emploi
            $emploi = $this->emploiRepository->findActiveByUserUuid($command->userUuid);

            if (!$emploi) {
                $emploi                                      = new Emploi;
                $emploi->{EmploiSchema::ID}                  = \Ramsey\Uuid\Uuid::uuid4()->toString();
                $emploi->{EmploiSchema::USER_PROFIL_KEY}     = $userProfil->{UserProfilSchema::PRIMARY_KEY};
                $emploi->{EmploiSchema::USER_PROFIL_ID}      = $userProfil->{UserProfilSchema::ID};
                $emploi->{EmploiSchema::STATUT}              = 'ACTIF';
                $emploi->{EmploiSchema::DATE_CREATION_POSTE} = \Illuminate\Support\Carbon::now();
            }

            // Mise à jour des données
            if ($command->titrePoste !== null) {
                $emploi->{EmploiSchema::TITRE_POSTE} = $command->titrePoste;
            }

            if ($command->description !== null) {
                $emploi->{EmploiSchema::DESCRIPTION} = $command->description;
            }

            if ($command->categorie !== null) {
                $emploi->{EmploiSchema::CATEGORIE} = $command->categorie;
            }

            if ($command->niveau !== null) {
                $emploi->{EmploiSchema::NIVEAU} = $command->niveau;
            }

            if ($command->typeContrat !== null) {
                $emploi->{EmploiSchema::TYPE_CONTRAT} = $command->typeContrat;
            }

            if ($command->tempsTravail !== null) {
                $emploi->{EmploiSchema::TEMPS_TRAVAIL} = $command->tempsTravail;
            }

            if ($command->cinemaId !== null) {
                $emploi->{EmploiSchema::CINEMA_ID} = $command->cinemaId;
            }

            if ($command->salaireMensuel !== null) {
                $salaireEnCentimes                               = (int) ($command->salaireMensuel * 100);
                $emploi->{EmploiSchema::SALAIRE_MIN_HT_CENTIMES} = $salaireEnCentimes;
                $emploi->{EmploiSchema::SALAIRE_MAX_HT_CENTIMES} = $salaireEnCentimes;
            }

            if ($command->dateEmbauche !== null) {
                $emploi->{EmploiSchema::DATE_EMBAUCHE} = \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $command->dateEmbauche);
            }

            if ($command->encadrementEquipe !== null) {
                $emploi->{EmploiSchema::ENCADREMENT_EQUIPE} = $command->encadrementEquipe;
            }

            if ($command->nombrePersonnesEncadrees !== null) {
                $emploi->{EmploiSchema::NOMBRE_PERSONNES_ENCADREES} = $command->nombrePersonnesEncadrees;
            }

            if ($command->travailWeekend !== null) {
                $emploi->{EmploiSchema::TRAVAIL_WEEKEND} = $command->travailWeekend;
            }

            if ($command->travailFeries !== null) {
                $emploi->{EmploiSchema::TRAVAIL_FERIES} = $command->travailFeries;
            }

            if ($command->travailSoiree !== null) {
                $emploi->{EmploiSchema::TRAVAIL_SOIREE} = $command->travailSoiree;
            }

            // Sauvegarder via le repository
            $savedEmploi = $this->emploiRepository->save($emploi);

            return Result::success($savedEmploi);

        } catch (Exception $e) {
            return Result::error('UPDATE_FAILED', $e->getMessage());
        }
    }
}
