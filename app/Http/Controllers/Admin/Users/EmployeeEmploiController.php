<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Users;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Application\Bus\QueryBus;
use App\Application\Bus\CommandBus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Domain\Employees\Enums\TypeContratEnum;
use App\Infrastructure\Database\Models\Cinema\Cinema;
use App\Http\Mappers\Users\GetUserDetailRequestMapper;
use App\Infrastructure\Database\Models\Employees\Emploi;
use App\Infrastructure\Database\Schemas\Cinema\CinemaSchema;
use App\Http\Mappers\Employees\UpdateEmployeeJobRequestMapper;
use App\Infrastructure\Database\Schemas\Employees\EmploiSchema;
use App\Infrastructure\Database\Schemas\Profiles\UserProfilSchema;

final class EmployeeEmploiController extends Controller
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly CommandBus $commandBus,
    ) {}

    /**
     * Show the form for editing the employee job information.
     */
    public function edit(Request $request, string $uuid): View
    {
        // 1. Récupérer les infos employé
        $query  = GetUserDetailRequestMapper::toQueryForEdit($request, $uuid);
        $result = $this->queryBus->ask($query);

        if ($result->isError()) {
            if ($result->getError() === 'USER_NOT_FOUND') {
                abort(404, 'Employé non trouvé');
            }
            abort(500, $result->getErrorMessage() ?: 'Erreur lors de la récupération de l\'employé');
        }

        $employee = $result->getValue();

        // Vérifier que c'est bien un employé
        if (!$employee->isEmployee()) {
            abort(404, 'Employé non trouvé');
        }

        // 2. Récupérer l'emploi actuel s'il existe
        $emploi = Emploi::query()
            ->join(
                UserProfilSchema::FULL_TABLE,
                EmploiSchema::FULL_TABLE . '.' . EmploiSchema::USER_PROFIL_KEY,
                '=',
                UserProfilSchema::FULL_TABLE . '.' . UserProfilSchema::PRIMARY_KEY
            )
            ->where(UserProfilSchema::FULL_TABLE . '.' . UserProfilSchema::USER_ID, $uuid)
            ->where(EmploiSchema::FULL_TABLE . '.' . EmploiSchema::STATUT, 'ACTIF')
            ->select(EmploiSchema::FULL_TABLE . '.*')
            ->first();

        // 3. Récupérer la liste des cinémas
        $cinemas = Cinema::where('est_actif', true)
            ->orderBy('nom')
            ->get();

        return view('admin.users.employees.emploi-edit', [
            'employee' => $employee,
            'emploi'   => $emploi,
            'cinemas'  => $cinemas,
        ]);
    }

    /**
     * Update the employee job information.
     */
    public function update(Request $request, string $uuid): RedirectResponse
    {
        // Validation
        $validated = $request->validate([
            'titre_poste'                => 'required|string|max:100',
            'categorie'                  => 'required|string|in:DIRECTION,ENCADREMENT,ACCUEIL_BILLETTERIE,PROJECTION,ENTRETIEN,SECURITE,TECHNIQUE,ADMINISTRATIF,ANIMATION,RESTAURATION',
            'niveau'                     => 'required|string|in:STAGIAIRE,JUNIOR,CONFIRME,SENIOR,EXPERT,RESPONSABLE,MANAGER,DIRECTEUR',
            'type_contrat'               => 'required|string|in:' . implode(',', TypeContratEnum::values()),
            'temps_travail'              => 'required|string|in:TEMPS_PLEIN,TEMPS_PARTIEL,HORAIRES_VARIABLES,SAISONNIER',
            'cinema_id'                  => ['required', 'uuid', Rule::exists(Cinema::class, CinemaSchema::ID)],
            'salaire_mensuel'            => 'nullable|numeric|min:0',
            'date_embauche'              => 'nullable|date|before_or_equal:today',
            'description'                => 'nullable|string|max:2000',
            'encadrement_equipe'         => 'boolean',
            'nombre_personnes_encadrees' => 'nullable|integer|min:0',
            'travail_weekend'            => 'boolean',
            'travail_feries'             => 'boolean',
            'travail_soiree'             => 'boolean',
        ]);

        // 1. Mapper Request → Command
        $command = UpdateEmployeeJobRequestMapper::toCommand($request, $uuid);

        // 2. Dispatch via CommandBus
        $result = $this->commandBus->dispatch($command);

        if ($result->isError()) {
            if ($result->getError() === 'VALIDATION_FAILED') {
                return redirect()
                    ->back()
                    ->withErrors($result->getErrorMessage())
                    ->withInput();
            }

            return redirect()
                ->back()
                ->withErrors(['error' => $result->getErrorMessage() ?: 'Erreur lors de la mise à jour'])
                ->withInput();
        }

        return redirect()
            ->route('admin.users.employees.show', $uuid)
            ->with('success', 'Fiche emploi mise à jour avec succès');
    }
}
