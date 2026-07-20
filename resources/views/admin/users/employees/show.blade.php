@extends('layouts.admin')

@section('title', 'Détails Employé')

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        {{-- <a href="#" class="hover:text-gray-900">Utilisateurs</a>
        <span>/</span> --}}
        <a href="{{ route('admin.users.employees.index') }}" class="hover:text-gray-900">Employés</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Détails</span>
    </nav>
@endsection

@section('subtitle', 'Informations détaillées de l\'employé')

@section('actions')
    <div class="flex space-x-3">
        <x-button href="{{ route('admin.users.employees.edit', $employee->uuid) }}" theme="admin" icon="heroicon-o-user">
            Modifier profil
        </x-button>
        <x-button href="{{ route('admin.users.employees.emploi.edit', $employee->uuid) }}" theme="admin" icon="heroicon-o-briefcase">
            Modifier fiche emploi
        </x-button>
    </div>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Informations principales -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Informations personnelles</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                        <p class="text-sm text-gray-900">{{ $employee->prenom }} {{ $employee->nom }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <p class="text-sm text-gray-900">{{ $employee->email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                        <p class="text-sm text-gray-900">{{ $employee->telephone ?: 'Non renseigné' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $employee->estActif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $employee->estActif ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>

                    @if($employee->dateNaissance)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de naissance</label>
                        <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($employee->dateNaissance)->format('d/m/Y') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informations professionnelles -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Informations professionnelles</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Poste</label>
                        <p class="text-sm text-gray-900">{{ $employee->poste ?: 'Non renseigné' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Département</label>
                        <p class="text-sm text-gray-900">{{ $employee->departement ?: 'Non renseigné' }}</p>
                    </div>

                    @if($employee->dateEmbauche)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'embauche</label>
                        <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($employee->dateEmbauche)->format('d/m/Y') }}</p>
                    </div>
                    @endif

                    @if($employee->salaire)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Salaire</label>
                        <p class="text-sm text-gray-900">{{ number_format($employee->salaire, 2, ',', ' ') }} €</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Adresse -->
        @if($employee->adresse || $employee->ville)
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Adresse</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                        <p class="text-sm text-gray-900">{{ $employee->adresse ?: 'Non renseignée' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                        <p class="text-sm text-gray-900">{{ $employee->ville ?: 'Non renseignée' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                        <p class="text-sm text-gray-900">{{ $employee->codePostal ?: 'Non renseigné' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                        <p class="text-sm text-gray-900">{{ $employee->pays }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif


        <!-- Métadonnées -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Informations système</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type d'utilisateur</label>
                        <p class="text-sm text-gray-900">{{ $employee->typeLabel }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email vérifié</label>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $employee->emailVerified ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $employee->emailVerified ? 'Vérifié' : 'Non vérifié' }}
                        </span>
                    </div>

                    @if($employee->createdAt)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Créé le</label>
                        <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($employee->createdAt)->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif

                    @if($employee->updatedAt)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dernière modification</label>
                        <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($employee->updatedAt)->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
