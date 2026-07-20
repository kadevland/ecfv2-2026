@extends('layouts.admin')

@section('title', 'Modifier le Client')

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        {{-- <a href="#" class="hover:text-gray-900">Utilisateurs</a>
        <span>/</span> --}}
        <a href="{{ route('admin.users.clients.index') }}" class="hover:text-gray-900">Clients</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Modifier</span>
    </nav>
@endsection

@section('subtitle', 'Modifier les informations du client')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Form Card -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Informations du client</h2>
                <p class="text-sm text-gray-600">Modifiez les informations du client</p>
            </div>

            <form method="POST" action="{{ route('admin.users.clients.show', $uuid) }}" class="p-6 space-y-6">
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
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900">Informations personnelles</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Prénom -->
                        <div>
                            <label for="prenom" class="block text-sm font-medium text-gray-700 mb-2">
                                Prénom <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="prenom"
                                id="prenom"
                                value="{{ old('prenom', $client->prenom) }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            >
                            @error('prenom')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nom -->
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700 mb-2">
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="nom"
                                id="nom"
                                value="{{ old('nom', $client->nom) }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            >
                            @error('nom')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email', $client->email) }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                required
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Téléphone -->
                        <div>
                            <label for="telephone" class="block text-sm font-medium text-gray-700 mb-2">
                                Téléphone
                            </label>
                            <input
                                type="tel"
                                name="telephone"
                                id="telephone"
                                value="{{ old('telephone', $client->telephone) }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="+33 1 23 45 67 89"
                            >
                            @error('telephone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date de naissance -->
                        <div>
                            <label for="dateNaissance" class="block text-sm font-medium text-gray-700 mb-2">
                                Date de naissance
                            </label>
                            <input
                                type="date"
                                name="dateNaissance"
                                id="dateNaissance"
                                value="{{ old('dateNaissance', $client->dateNaissance) }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                            @error('dateNaissance')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sexe -->
                        <div>
                            <label for="sexe" class="block text-sm font-medium text-gray-700 mb-2">
                                Sexe
                            </label>
                            <select
                                name="sexe"
                                id="sexe"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">Sélectionner</option>
                                @foreach(\App\Domain\Shared\Enums\SexeEnum::options() as $value => $label)
                                    <option value="{{ $value }}" {{ old('sexe', $client->sexe) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('sexe')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div>
                            <label for="estActif" class="block text-sm font-medium text-gray-700 mb-2">
                                Statut du compte
                            </label>
                            <select
                                name="estActif"
                                id="estActif"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="1" {{ old('estActif', $client->estActif) ? 'selected' : '' }}>Actif</option>
                                <option value="0" {{ !old('estActif', $client->estActif) ? 'selected' : '' }}>Inactif</option>
                            </select>
                            @error('estActif')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Adresse -->
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900">Adresse</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Adresse -->
                        <div class="md:col-span-2">
                            <label for="adresse" class="block text-sm font-medium text-gray-700 mb-2">
                                Adresse
                            </label>
                            <input
                                type="text"
                                name="adresse"
                                id="adresse"
                                value="{{ old('adresse', $client->adresse) }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="123 rue de la Paix"
                            >
                            @error('adresse')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Ville -->
                        <div>
                            <label for="ville" class="block text-sm font-medium text-gray-700 mb-2">
                                Ville
                            </label>
                            <input
                                type="text"
                                name="ville"
                                id="ville"
                                value="{{ old('ville', $client->ville) }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Paris"
                            >
                            @error('ville')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Code postal -->
                        <div>
                            <label for="codePostal" class="block text-sm font-medium text-gray-700 mb-2">
                                Code postal
                            </label>
                            <input
                                type="text"
                                name="codePostal"
                                id="codePostal"
                                value="{{ old('codePostal', $client->codePostal) }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="75001"
                                maxlength="10"
                            >
                            @error('codePostal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pays -->
                        <div>
                            <label for="pays" class="block text-sm font-medium text-gray-700 mb-2">
                                Pays
                            </label>
                            <select
                                name="pays"
                                id="pays"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="FR" {{ old('pays', $client->pays) === 'FR' ? 'selected' : '' }}>France</option>
                                <option value="BE" {{ old('pays', $client->pays) === 'BE' ? 'selected' : '' }}>Belgique</option>
                                <option value="CH" {{ old('pays', $client->pays) === 'CH' ? 'selected' : '' }}>Suisse</option>
                                <option value="CA" {{ old('pays', $client->pays) === 'CA' ? 'selected' : '' }}>Canada</option>
                            </select>
                            @error('pays')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>


                <!-- Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a
                        href="{{ route('admin.users.clients.show', $uuid) }}"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Annuler
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
