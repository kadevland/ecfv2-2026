@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="container mx-auto px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Welcome Header -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4">
                <h1 class="text-2xl font-bold text-gray-900">
                    Bienvenue {{ $profile['full_name'] ?? 'Utilisateur' }}
                </h1>
                <p class="text-gray-600 mt-1">
                    Tableau de bord - {{ ucfirst($profile['type'] ?? 'utilisateur') }}
                </p>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- User Profile Card -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-medium text-gray-900">Informations du profil</h2>
                </div>
                <div class="px-6 py-4">
                    @if($profile)
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Type de compte</dt>
                                <dd class="text-sm text-gray-900">{{ ucfirst($profile['type']) }}</dd>
                            </div>

                            @if(isset($profile['nom']) && isset($profile['prenom']))
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nom complet</dt>
                                <dd class="text-sm text-gray-900">{{ $profile['prenom'] }} {{ $profile['nom'] }}</dd>
                            </div>
                            @endif

                            @if(isset($profile['email']))
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="text-sm text-gray-900">{{ $profile['email'] }}</dd>
                            </div>
                            @endif

                            @if(isset($profile['poste']))
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Poste</dt>
                                <dd class="text-sm text-gray-900">{{ $profile['poste'] }}</dd>
                            </div>
                            @endif

                            @if(isset($profile['departement']))
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Département</dt>
                                <dd class="text-sm text-gray-900">{{ $profile['departement'] }}</dd>
                            </div>
                            @endif
                        </dl>
                    @else
                        <p class="text-gray-600">Aucune information de profil disponible.</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-medium text-gray-900">Actions rapides</h2>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-3">
                        @if($profile && $profile['type'] === 'client')
                            <a href="#" class="block w-full text-left px-4 py-2 bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition duration-200">
                                Réserver une séance
                            </a>
                            <a href="#" class="block w-full text-left px-4 py-2 bg-green-50 text-green-700 rounded-md hover:bg-green-100 transition duration-200">
                                Mes réservations
                            </a>
                            <a href="#" class="block w-full text-left px-4 py-2 bg-purple-50 text-purple-700 rounded-md hover:bg-purple-100 transition duration-200">
                                Catalogue des films
                            </a>
                        @elseif($profile && in_array($profile['type'], ['employee', 'admin']))
                            <a href="#" class="block w-full text-left px-4 py-2 bg-orange-50 text-orange-700 rounded-md hover:bg-orange-100 transition duration-200">
                                Gestion des séances
                            </a>
                            <a href="#" class="block w-full text-left px-4 py-2 bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition duration-200">
                                Rapports d'incidents
                            </a>
                            @if($profile['type'] === 'admin')
                            <a href="#" class="block w-full text-left px-4 py-2 bg-gray-50 text-gray-700 rounded-md hover:bg-gray-100 transition duration-200">
                                Administration
                            </a>
                            @endif
                        @endif

                        <a href="#" class="block w-full text-left px-4 py-2 bg-gray-50 text-gray-700 rounded-md hover:bg-gray-100 transition duration-200">
                            Modifier mon profil
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity (placeholder) -->
        <div class="bg-white shadow rounded-lg mt-6">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-medium text-gray-900">Activité récente</h2>
            </div>
            <div class="px-6 py-4">
                <p class="text-gray-600 text-center py-8">
                    Aucune activité récente à afficher.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection