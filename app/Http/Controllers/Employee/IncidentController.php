<?php

declare(strict_types=1);

namespace App\Http\Controllers\Employee;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Domain\Enums\TypeIncident;
use App\Domain\Enums\StatutIncident;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Domain\Enums\SeveriteIncident;
use App\Http\Requests\Employee\StoreIncidentRequest;
use App\Infrastructure\Database\Models\Employees\Incident;

final class IncidentController extends Controller
{
    public function index(Request $request): View
    {
        // Récupérer les incidents récents directement avec Eloquent
        $query = Incident::with([
            'emploiDeclarant.profil',
            'cinema',
            'salle',
            'responsableResolution',
        ])
            ->where('created_at', '>=', now()->subDays(30)); // Incidents du mois

        // Appliquer les filtres (mapping des valeurs du domaine vers DB)
        if ($request->filled('statut')) {
            // Mapper statut du domaine vers DB
            $statutDomaine = $request->input('statut');
            $statutDb      = match ($statutDomaine) {
                'nouveau'  => 'NOUVEAU',
                'en_cours' => 'EN_COURS',
                'resolu'   => 'RESOLU',
                'ferme'    => 'CLOS',
                default    => 'NOUVEAU'
            };
            $query->where('statut', $statutDb);
        }

        if ($request->filled('type')) {
            // Mapper type du domaine vers DB
            $typeDomaine = $request->input('type');
            $typeDb      = match ($typeDomaine) {
                'technique' => 'TECHNIQUE',
                'securite'  => 'SECURITE',
                'client'    => 'CLIENTELE',
                default     => $typeDomaine
            };
            $query->where('type_incident', $typeDb);
        }

        if ($request->filled('severite')) {
            // Mapper sévérité du domaine vers DB
            $severiteDomaine = $request->input('severite');
            $severiteDb      = match ($severiteDomaine) {
                'faible'   => 'MINEUR',
                'moyenne'  => 'MODERE',
                'haute'    => 'MAJEUR',
                'critique' => 'CRITIQUE',
                default    => $severiteDomaine
            };
            $query->where('niveau_gravite', $severiteDb);
        }

        $incidents = $query->orderBy('created_at', 'desc')->paginate(20);

        // Options pour les filtres utilisant les enums du domaine
        $statutsDisponibles = [];
        foreach (StatutIncident::cases() as $statut) {
            $statutsDisponibles[$statut->value] = $statut->label();
        }

        $typesDisponibles = [];
        foreach (TypeIncident::cases() as $type) {
            $typesDisponibles[$type->value] = $type->label();
        }

        $severitesDisponibles = [];
        foreach (SeveriteIncident::cases() as $severite) {
            $severitesDisponibles[$severite->value] = $severite->label();
        }

        return view('employee.incidents.index', [
            'incidents'            => $incidents,
            'statutsDisponibles'   => $statutsDisponibles,
            'typesDisponibles'     => $typesDisponibles,
            'severitesDisponibles' => $severitesDisponibles,
            'filters'              => $request->all(['statut', 'type', 'severite']),
            'dateJour'             => now()->format('d/m/Y'),
        ]);
    }

    public function create(): View
    {
        // Options pour le formulaire utilisant les enums du domaine
        $typesDisponibles = [];
        foreach (TypeIncident::cases() as $type) {
            $typesDisponibles[$type->value] = $type->label();
        }

        $severiteDisponibles = [];
        foreach (SeveriteIncident::cases() as $severite) {
            $severiteDisponibles[$severite->value] = $severite->label();
        }

        // Récupérer les salles du cinéma de l'employé connecté
        $user  = auth()->user();
        $query = \App\Infrastructure\Database\Models\Cinema\Salle::on('pgsql')
            ->select('db_id', 'nom');

        // Filtrer par cinéma si on a l'info de l'employé
        if ($user && isset($user->emploi) && isset($user->emploi->cinema_uuid)) {
            $query->where('cinema_uuid', $user->emploi->cinema_uuid);
        }

        $sallesDisponibles = $query->orderBy('nom')
            ->pluck('nom', 'db_id')
            ->toArray();

        return view('employee.incidents.create', [
            'typesDisponibles'    => $typesDisponibles,
            'severiteDisponibles' => $severiteDisponibles,
            'sallesDisponibles'   => $sallesDisponibles,
        ]);
    }

    public function store(StoreIncidentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user      = auth()->user();

        // Mapper les enums vers les valeurs DB
        $typeDbValue = match ($validated['type_incident']) {
            'technique' => 'TECHNIQUE',
            'securite'  => 'SECURITE',
            'client'    => 'CLIENTELE',
            default     => 'TECHNIQUE'
        };

        $severiteDbValue = match ($validated['severite']) {
            'faible'   => 'MINEUR',
            'moyenne'  => 'MODERE',
            'haute'    => 'MAJEUR',
            'critique' => 'CRITIQUE',
            default    => 'MODERE'
        };

        // Données essentielles seulement
        $dataToInsert = [
            'uuid'            => \Illuminate\Support\Str::uuid(),
            'numero_incident' => 'INC-' . date('Y') . '-' . str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT),
            'titre'           => $validated['titre'],
            'description'     => $validated['description'],
            'type_incident'   => $typeDbValue,
            'niveau_gravite'  => $severiteDbValue,

            // Defaults obligatoires pour la DB
            'categorie'               => 'GENERAL',
            'niveau_priorite'         => 'NORMALE',
            'statut'                  => 'NOUVEAU',
            'date_incident'           => now(),
            'date_rapport'            => now(),
            'contrat_rapporteur_id'   => 1, // TODO: utiliser l'employé connecté
            'cinema_db_id'            => 1,
            'cinema_uuid'             => $user->emploi->cinema_uuid ?? \Illuminate\Support\Str::uuid(),
            'degats_materiels'        => false,
            'cout_degats_centimes'    => 0,
            'devise'                  => 'EUR',
            'assurance_impliquee'     => false,
            'formation_requise'       => false,
            'declaration_obligatoire' => false,
            'declaration_effectuee'   => false,
        ];

        // Salle optionnelle
        if (!empty($validated['salle_db_id'])) {
            $dataToInsert['salle_db_id'] = $validated['salle_db_id'];
        }

        Incident::create($dataToInsert);

        return redirect()
            ->route('employee.incidents.index')
            ->with('success', 'Incident créé avec succès.');
    }
}
