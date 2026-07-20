@extends('layouts.employee')

@section('title', 'Séances du jour')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold leading-tight text-gray-900">
                🎬 Séances du jour
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                {{ $date->locale('fr')->isoFormat('dddd D MMMM YYYY') }} • {{ $cinema_name }}
            </p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-500">
                {{ $total_seances }} séance(s) • {{ number_format($total_revenue, 2) }}€ de CA
            </div>
            <a href="{{ route('employee.dashboard') }}" class="bg-white border border-gray-300 rounded-md shadow-sm px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                ← Dashboard
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total séances</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $total_seances }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Réservations</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $total_reservations }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Chiffre d'affaires</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($total_revenue, 2) }}€</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Occupation moyenne</dt>
                            <dd class="text-lg font-medium text-gray-900">
                                @php
                                    $avgOccupancy = 0;
                                    if (count($seances) > 0) {
                                        $totalOccupancy = array_sum(array_column($seances, 'occupancy_rate'));
                                        $avgOccupancy = $totalOccupancy / count($seances);
                                    }
                                @endphp
                                {{ number_format($avgOccupancy, 1) }}%
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des séances -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Planning détaillé des séances
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Toutes les séances programmées pour {{ $date->locale('fr')->isoFormat('dddd D MMMM') }}
            </p>
        </div>

        <ul class="divide-y divide-gray-200">
            @forelse($seances as $seance)
                <li class="px-4 py-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- Heure -->
                            <div class="flex-shrink-0">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-gray-900">
                                        {{ $seance['date_heure_debut']->format('H:i') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $seance['date_heure_fin']->format('H:i') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Informations film -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2">
                                    <h4 class="text-base font-medium text-gray-900 truncate">
                                        {{ $seance['film_titre'] }}
                                    </h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $seance['film_genre'] }}
                                    </span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $seance['version'] }}
                                    </span>
                                    @if($seance['qualite_projection'] !== 'Standard')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $seance['qualite_projection'] }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-4 mt-1 text-sm text-gray-500">
                                    <span>🏠 {{ $seance['salle'] }}</span>
                                    <span>⏱️ {{ $seance['film_duree'] }}min</span>
                                    <span>💺 {{ $seance['capacite_salle'] ?? 100 }} places</span>
                                </div>
                            </div>
                        </div>

                        <!-- Statut et métriques -->
                        <div class="flex items-center space-x-6">
                            <!-- Réservations -->
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ $seance['reservations_count'] ?? 0 }}
                                </div>
                                <div class="text-xs text-gray-500">réservations</div>
                            </div>

                            <!-- Places vendues -->
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ $seance['places_vendues'] ?? 0 }}
                                </div>
                                <div class="text-xs text-gray-500">places vendues</div>
                            </div>

                            <!-- Taux d'occupation -->
                            <div class="text-center">
                                <div class="text-lg font-semibold {{ ($seance['occupancy_rate'] ?? 0) >= 80 ? 'text-green-600' : (($seance['occupancy_rate'] ?? 0) >= 50 ? 'text-yellow-600' : 'text-gray-600') }}">
                                    {{ number_format($seance['occupancy_rate'] ?? 0, 1) }}%
                                </div>
                                <div class="text-xs text-gray-500">occupation</div>
                            </div>

                            <!-- Revenus -->
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ number_format($seance['revenue'] ?? 0, 0) }}€
                                </div>
                                <div class="text-xs text-gray-500">revenus</div>
                            </div>

                            <!-- Statut -->
                            <div class="flex-shrink-0">
                                @if($seance['date_heure_debut']->isPast())
                                    @if($seance['date_heure_fin']->isPast())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            ✓ Terminée
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            🔴 En cours
                                        </span>
                                    @endif
                                @elseif($seance['date_heure_debut']->diffInMinutes(now()) <= 30)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        ⏰ Bientôt
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        📅 Programmée
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Barre de progression occupation -->
                    <div class="mt-3">
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                            <span>Occupation</span>
                            <span>{{ $seance['places_vendues'] ?? 0 }}/{{ $seance['capacite_salle'] ?? 100 }} places</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full {{ ($seance['occupancy_rate'] ?? 0) >= 80 ? 'bg-green-500' : (($seance['occupancy_rate'] ?? 0) >= 50 ? 'bg-yellow-500' : 'bg-blue-500') }}"
                                 style="width: {{ min($seance['occupancy_rate'] ?? 0, 100) }}%"></div>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-4 py-8">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune séance</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Aucune séance n'est programmée pour aujourd'hui.
                        </p>
                    </div>
                </li>
            @endforelse
        </ul>
    </div>

    <!-- Actions rapides -->
    <div class="mt-8 flex justify-center space-x-4">
        <a href="{{ route('employee.seances.week') }}" class="bg-blue-600 border border-transparent rounded-md shadow-sm px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            📆 Voir la semaine
        </a>
        <a href="{{ route('employee.reservations.today') }}" class="bg-green-600 border border-transparent rounded-md shadow-sm px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
            🎫 Voir les réservations
        </a>
    </div>
</div>
@endsection