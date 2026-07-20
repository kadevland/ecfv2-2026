@extends('layouts.admin')

@section('title', 'Détails Client')

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        {{-- <a href="#" class="hover:text-gray-900">Utilisateurs</a>
        <span>/</span> --}}
        <a href="{{ route('admin.users.clients.index') }}" class="hover:text-gray-900">Clients</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Détails</span>
    </nav>
@endsection

@section('subtitle', 'Informations détaillées du client')

@section('actions')
    <x-button href="{{ route('admin.users.clients.edit', $client->uuid) }}" theme="admin" icon="heroicon-o-pencil">
        Modifier
    </x-button>
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
                        <p class="text-sm text-gray-900">{{ $client->prenom }} {{ $client->nom }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <p class="text-sm text-gray-900">{{ $client->email }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                        <p class="text-sm text-gray-900">{{ $client->telephone ?: 'Non renseigné' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $client->estActif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $client->estActif ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>

                    @if($client->dateNaissance)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de naissance</label>
                        <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($client->dateNaissance)->format('d/m/Y') }}</p>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type de compte</label>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $client->typeLabel }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations compte -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Informations du compte</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email vérifié</label>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $client->emailVerified ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                            {{ $client->emailVerified ? 'Vérifié' : 'Non vérifié' }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'inscription</label>
                        <p class="text-sm text-gray-900">
                            @if($client->createdAt)
                                {{ \Carbon\Carbon::parse($client->createdAt)->format('d/m/Y à H:i') }}
                            @else
                                Non renseignée
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dernière modification</label>
                        <p class="text-sm text-gray-900">
                            @if($client->updatedAt)
                                {{ \Carbon\Carbon::parse($client->updatedAt)->format('d/m/Y à H:i') }}
                            @else
                                Non renseignée
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Adresse -->
        @if($client->adresse || $client->ville)
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Adresse</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                        <p class="text-sm text-gray-900">{{ $client->adresse ?: 'Non renseignée' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                        <p class="text-sm text-gray-900">{{ $client->ville ?: 'Non renseignée' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                        <p class="text-sm text-gray-900">{{ $client->codePostal ?: 'Non renseigné' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pays</label>
                        <p class="text-sm text-gray-900">{{ $client->pays }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
@endsection
