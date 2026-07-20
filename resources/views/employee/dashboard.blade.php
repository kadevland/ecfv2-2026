@extends('layouts.employee' ,['cinema_name'=>  $employee['cinema'] ?? ''])

@section('title', 'Dashboard Employé')

@section('content')
<div class="py-6">
    <!-- Header avec info employé -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Bonjour, {{ $employee['prenom'] }} {{ $employee['nom'] }}
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ $employee['poste'] }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Compteurs du jour -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 16h4m10 0h4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Séances du jour</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $nbSeancesDuJour }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Réservations du jour</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $nbReservationsDuJour }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16l5.5-5 5.5 5V4z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Films du jour</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $nbFilmsDuJour }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 10 prochaines séances -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Prochaines séances</h2>
                    <a href="{{ route('employee.seances.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        Voir toutes les séances →
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($prochainesSeances->count() > 0)
                    <div class="space-y-4">
                        @foreach($prochainesSeances as $seance)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900">{{ $seance->film->titre ?? 'Film non trouvé' }}</h3>
                                <p class="text-sm text-gray-600">
                                    Salle {{ $seance->salle->nom ?? 'Non définie' }} -
                                    {{ $seance->date_heure_debut->format('H:i') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $seance->date_heure_debut->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 py-8">
                        Aucune séance programmée prochainement
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
