@extends('layouts.admin')

@section('title', 'Modifier la fiche emploi')

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        {{-- <a href="#" class="hover:text-gray-900">Utilisateurs</a>
        <span>/</span> --}}
        <a href="{{ route('admin.users.employees.index') }}" class="hover:text-gray-900">Employés</a>
        <span>/</span>
        <a href="{{ route('admin.users.employees.show', $employee->uuid) }}" class="hover:text-gray-900">{{ $employee->prenom }} {{ $employee->nom }}</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Fiche emploi</span>
    </nav>
@endsection

@section('subtitle', 'Modifier les informations professionnelles de l\'employé')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Form Card -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Informations professionnelles</h2>
                <p class="text-sm text-gray-600">Gérer le poste et les informations d'emploi</p>
            </div>

            <form method="POST" action="{{ route('admin.users.employees.emploi.update', $employee->uuid) }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Display validation errors -->
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Erreurs de validation</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul role="list" class="list-disc space-y-1 pl-5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Information employé (lecture seule) -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Employé</h3>
                    <p class="text-sm text-gray-600">{{ $employee->prenom }} {{ $employee->nom }}</p>
                    <p class="text-xs text-gray-500">{{ $employee->email }}</p>
                </div>

                <!-- Poste actuel -->
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900">Poste</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="titre_poste" class="block text-sm font-medium text-gray-700 mb-2">Titre du poste *</label>
                            <input
                                type="text"
                                id="titre_poste"
                                name="titre_poste"
                                value="{{ old('titre_poste', $emploi->titre_poste ?? '') }}"
                                required
                                placeholder="Ex: Responsable billetterie"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <div>
                            <label for="categorie" class="block text-sm font-medium text-gray-700 mb-2">Catégorie *</label>
                            <select
                                id="categorie"
                                name="categorie"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">Sélectionner une catégorie</option>
                                <option value="DIRECTION" {{ old('categorie', $emploi->categorie ?? '') === 'DIRECTION' ? 'selected' : '' }}>Direction</option>
                                <option value="ENCADREMENT" {{ old('categorie', $emploi->categorie ?? '') === 'ENCADREMENT' ? 'selected' : '' }}>Encadrement</option>
                                <option value="ACCUEIL_BILLETTERIE" {{ old('categorie', $emploi->categorie ?? '') === 'ACCUEIL_BILLETTERIE' ? 'selected' : '' }}>Accueil / Billetterie</option>
                                <option value="PROJECTION" {{ old('categorie', $emploi->categorie ?? '') === 'PROJECTION' ? 'selected' : '' }}>Projection</option>
                                <option value="ENTRETIEN" {{ old('categorie', $emploi->categorie ?? '') === 'ENTRETIEN' ? 'selected' : '' }}>Entretien</option>
                                <option value="SECURITE" {{ old('categorie', $emploi->categorie ?? '') === 'SECURITE' ? 'selected' : '' }}>Sécurité</option>
                                <option value="TECHNIQUE" {{ old('categorie', $emploi->categorie ?? '') === 'TECHNIQUE' ? 'selected' : '' }}>Technique</option>
                                <option value="ADMINISTRATIF" {{ old('categorie', $emploi->categorie ?? '') === 'ADMINISTRATIF' ? 'selected' : '' }}>Administratif</option>
                                <option value="ANIMATION" {{ old('categorie', $emploi->categorie ?? '') === 'ANIMATION' ? 'selected' : '' }}>Animation</option>
                                <option value="RESTAURATION" {{ old('categorie', $emploi->categorie ?? '') === 'RESTAURATION' ? 'selected' : '' }}>Restauration</option>
                            </select>
                        </div>

                        <div>
                            <label for="niveau" class="block text-sm font-medium text-gray-700 mb-2">Niveau *</label>
                            <select
                                id="niveau"
                                name="niveau"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">Sélectionner un niveau</option>
                                <option value="STAGIAIRE" {{ old('niveau', $emploi->niveau ?? '') === 'STAGIAIRE' ? 'selected' : '' }}>Stagiaire</option>
                                <option value="JUNIOR" {{ old('niveau', $emploi->niveau ?? '') === 'JUNIOR' ? 'selected' : '' }}>Junior</option>
                                <option value="CONFIRME" {{ old('niveau', $emploi->niveau ?? '') === 'CONFIRME' ? 'selected' : '' }}>Confirmé</option>
                                <option value="SENIOR" {{ old('niveau', $emploi->niveau ?? '') === 'SENIOR' ? 'selected' : '' }}>Senior</option>
                                <option value="EXPERT" {{ old('niveau', $emploi->niveau ?? '') === 'EXPERT' ? 'selected' : '' }}>Expert</option>
                                <option value="RESPONSABLE" {{ old('niveau', $emploi->niveau ?? '') === 'RESPONSABLE' ? 'selected' : '' }}>Responsable</option>
                                <option value="MANAGER" {{ old('niveau', $emploi->niveau ?? '') === 'MANAGER' ? 'selected' : '' }}>Manager</option>
                                <option value="DIRECTEUR" {{ old('niveau', $emploi->niveau ?? '') === 'DIRECTEUR' ? 'selected' : '' }}>Directeur</option>
                            </select>
                        </div>

                        <div>
                            <label for="type_contrat" class="block text-sm font-medium text-gray-700 mb-2">Type de contrat *</label>
                            <select
                                id="type_contrat"
                                name="type_contrat"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">Sélectionner un type</option>
                                <option value="CDI" {{ old('type_contrat', $emploi->type_contrat ?? '') === 'CDI' ? 'selected' : '' }}>CDI</option>
                                <option value="CDD" {{ old('type_contrat', $emploi->type_contrat ?? '') === 'CDD' ? 'selected' : '' }}>CDD</option>
                                <option value="INTERIM" {{ old('type_contrat', $emploi->type_contrat ?? '') === 'INTERIM' ? 'selected' : '' }}>Intérim</option>
                                <option value="STAGE" {{ old('type_contrat', $emploi->type_contrat ?? '') === 'STAGE' ? 'selected' : '' }}>Stage</option>
                                <option value="APPRENTISSAGE" {{ old('type_contrat', $emploi->type_contrat ?? '') === 'APPRENTISSAGE' ? 'selected' : '' }}>Apprentissage</option>
                                <option value="FREELANCE" {{ old('type_contrat', $emploi->type_contrat ?? '') === 'FREELANCE' ? 'selected' : '' }}>Freelance</option>
                            </select>
                        </div>

                        <div>
                            <label for="temps_travail" class="block text-sm font-medium text-gray-700 mb-2">Temps de travail *</label>
                            <select
                                id="temps_travail"
                                name="temps_travail"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">Sélectionner</option>
                                <option value="TEMPS_PLEIN" {{ old('temps_travail', $emploi->temps_travail ?? '') === 'TEMPS_PLEIN' ? 'selected' : '' }}>Temps plein</option>
                                <option value="TEMPS_PARTIEL" {{ old('temps_travail', $emploi->temps_travail ?? '') === 'TEMPS_PARTIEL' ? 'selected' : '' }}>Temps partiel</option>
                                <option value="HORAIRES_VARIABLES" {{ old('temps_travail', $emploi->temps_travail ?? '') === 'HORAIRES_VARIABLES' ? 'selected' : '' }}>Horaires variables</option>
                                <option value="SAISONNIER" {{ old('temps_travail', $emploi->temps_travail ?? '') === 'SAISONNIER' ? 'selected' : '' }}>Saisonnier</option>
                            </select>
                        </div>

                        <div>
                            <label for="cinema_id" class="block text-sm font-medium text-gray-700 mb-2">Cinéma d'affectation *</label>
                            <select
                                id="cinema_id"
                                name="cinema_id"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">Sélectionner un cinéma</option>
                                @foreach($cinemas as $cinema)
                                    @php
                                        $selectedValue = old('cinema_id') ?: ($emploi->cinema_uuid ?? '');
                                        $isSelected = trim($selectedValue) == trim($cinema->uuid);
                                    @endphp
                                    <option value="{{ $cinema->uuid }}" {{ $isSelected ? 'selected' : '' }}>
                                        {{ $cinema->nom }} - {{ $cinema->ville }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Salaire -->
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900">Rémunération</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="salaire_mensuel" class="block text-sm font-medium text-gray-700 mb-2">Salaire mensuel brut (€)</label>
                            <input
                                type="number"
                                id="salaire_mensuel"
                                name="salaire_mensuel"
                                value="{{ old('salaire_mensuel', $emploi && $emploi->salaire_min_ht_centimes ? number_format($emploi->salaire_min_ht_centimes / 100, 2, '.', '') : '') }}"
                                step="0.01"
                                min="0"
                                placeholder="Ex: 2500.00"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <div>
                            <label for="date_embauche" class="block text-sm font-medium text-gray-700 mb-2">Date d'embauche</label>
                            <input
                                type="date"
                                id="date_embauche"
                                name="date_embauche"
                                value="{{ old('date_embauche', $emploi && $emploi->date_embauche ? $emploi->date_embauche->format('Y-m-d') : '') }}"
                                max="{{ date('Y-m-d') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                    </div>
                </div>

                <!-- Responsabilités -->
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900">Responsabilités</h3>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description du poste</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Décrivez les missions et responsabilités principales..."
                        >{{ old('description', $emploi->description ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    name="encadrement_equipe"
                                    value="1"
                                    {{ old('encadrement_equipe', $emploi->encadrement_equipe ?? false) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                />
                                <span class="ml-2 text-sm text-gray-700">Encadrement d'équipe</span>
                            </label>
                        </div>

                        <div>
                            <label for="nombre_personnes_encadrees" class="block text-sm font-medium text-gray-700 mb-2">Nombre de personnes encadrées</label>
                            <input
                                type="number"
                                id="nombre_personnes_encadrees"
                                name="nombre_personnes_encadrees"
                                value="{{ old('nombre_personnes_encadrees', $emploi->nombre_personnes_encadrees ?? 0) }}"
                                min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                name="travail_weekend"
                                value="1"
                                {{ old('travail_weekend', $emploi->travail_weekend ?? false) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            />
                            <span class="ml-2 text-sm text-gray-700">Travail le weekend</span>
                        </label>

                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                name="travail_feries"
                                value="1"
                                {{ old('travail_feries', $emploi->travail_feries ?? false) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            />
                            <span class="ml-2 text-sm text-gray-700">Travail les jours fériés</span>
                        </label>

                        <label class="flex items-center">
                            <input
                                type="checkbox"
                                name="travail_soiree"
                                value="1"
                                {{ old('travail_soiree', $emploi->travail_soiree ?? false) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            />
                            <span class="ml-2 text-sm text-gray-700">Travail en soirée</span>
                        </label>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.users.employees.show', $employee->uuid) }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Annuler
                    </a>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Enregistrer la fiche emploi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
