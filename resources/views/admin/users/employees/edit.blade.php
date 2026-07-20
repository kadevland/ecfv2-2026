@extends('layouts.admin')

@section('title', 'Modifier l\'Employé')

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        {{-- <a href="#" class="hover:text-gray-900">Utilisateurs</a>
        <span>/</span> --}}
        <a href="{{ route('admin.users.employees.index') }}" class="hover:text-gray-900">Employés</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Modifier</span>
    </nav>
@endsection

@section('subtitle', 'Modifier les informations de l\'employé')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Form Card -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Informations de l'employé</h2>
                <p class="text-sm text-gray-600">Modifiez les informations de l'employé</p>
            </div>

            <form method="POST" action="{{ route('admin.users.employees.show', $uuid) }}" class="p-6 space-y-6">
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

                <!-- Informations personnelles -->
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900">Informations personnelles</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">Prénom *</label>
                            <input
                                type="text"
                                id="prenom"
                                name="prenom"
                                value="{{ old('prenom', $employee->prenom) }}"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                            <input
                                type="text"
                                id="nom"
                                name="nom"
                                value="{{ old('nom', $employee->nom) }}"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $employee->email) }}"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <div>
                            <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                            <input
                                type="tel"
                                id="telephone"
                                name="telephone"
                                value="{{ old('telephone', $employee->telephone) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <div>
                            <label for="date_naissance" class="block text-sm font-medium text-gray-700 mb-2">Date de naissance</label>
                            <input
                                type="date"
                                id="date_naissance"
                                name="date_naissance"
                                value="{{ old('date_naissance', $employee->dateNaissance) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <div>
                            <label for="sexe" class="block text-sm font-medium text-gray-700 mb-2">Sexe</label>
                            <select
                                id="sexe"
                                name="sexe"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">Sélectionner</option>
                                @foreach(\App\Domain\Shared\Enums\SexeEnum::options() as $value => $label)
                                    <option value="{{ $value }}" {{ old('sexe', $employee->sexe) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="est_actif" class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                            <select
                                id="est_actif"
                                name="est_actif"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="1" {{ old('est_actif', $employee->estActif ? '1' : '0') === '1' ? 'selected' : '' }}>Actif</option>
                                <option value="0" {{ old('est_actif', $employee->estActif ? '1' : '0') === '0' ? 'selected' : '' }}>Inactif</option>
                            </select>
                        </div>
                    </div>
                </div>


                <!-- Adresse -->
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900">Adresse</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="adresse" class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                            <input
                                type="text"
                                id="adresse"
                                name="adresse"
                                value="{{ old('adresse', $employee->adresse) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <div>
                            <label for="ville" class="block text-sm font-medium text-gray-700 mb-2">Ville</label>
                            <input
                                type="text"
                                id="ville"
                                name="ville"
                                value="{{ old('ville', $employee->ville) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <div>
                            <label for="code_postal" class="block text-sm font-medium text-gray-700 mb-2">Code postal</label>
                            <input
                                type="text"
                                id="code_postal"
                                name="code_postal"
                                value="{{ old('code_postal', $employee->codePostal) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                        </div>

                        <div>
                            <label for="pays" class="block text-sm font-medium text-gray-700 mb-2">Pays</label>
                            <select
                                id="pays"
                                name="pays"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="FR" {{ old('pays', $employee->pays) === 'FR' ? 'selected' : '' }}>France</option>
                                <option value="BE" {{ old('pays', $employee->pays) === 'BE' ? 'selected' : '' }}>Belgique</option>
                                <option value="CH" {{ old('pays', $employee->pays) === 'CH' ? 'selected' : '' }}>Suisse</option>
                                <option value="LU" {{ old('pays', $employee->pays) === 'LU' ? 'selected' : '' }}>Luxembourg</option>
                            </select>
                        </div>
                    </div>
                </div>


                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.users.employees.show', $uuid) }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Annuler
                    </a>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
