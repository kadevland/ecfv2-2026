@extends('layouts.admin')

@section('title', 'Dashboard Administrateur')

@section('content')
<div class="py-6">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Administrateur</h1>
            <p class="text-sm text-gray-600 mt-1">Vue d'ensemble de la chaîne Cinéphoria</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats génériques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Séances -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 16h4m10 0h4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Séances</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_seances'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Réservations -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Réservations</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_reservations'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Cinémas -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Cinémas</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_cinemas'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Films -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 16h4m10 0h4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Films</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_films'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="mt-8 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Actions rapides</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="/admin/films" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <h3 class="font-medium text-gray-900">Gérer les Films</h3>
                        <p class="text-sm text-gray-600">Ajouter, modifier ou supprimer des films</p>
                    </a>

                    <a href="/admin/seances" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <h3 class="font-medium text-gray-900">Gérer les Séances</h3>
                        <p class="text-sm text-gray-600">Planifier et organiser les séances</p>
                    </a>

                    <a href="/admin/reservations" class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <h3 class="font-medium text-gray-900">Voir les Réservations</h3>
                        <p class="text-sm text-gray-600">Consulter toutes les réservations</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
