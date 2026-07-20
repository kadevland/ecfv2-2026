@extends('layouts.employee')

@section('title', 'Séances de la semaine')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold leading-tight text-gray-900">
                📆 Séances de la semaine
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Du {{ $startDate->locale('fr')->isoFormat('dddd D MMMM') }} au {{ $endDate->locale('fr')->isoFormat('dddd D MMMM YYYY') }} • {{ $cinema_name }}
            </p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-500">
                {{ $total_seances }} séance(s) total
            </div>
            <a href="{{ route('employee.dashboard') }}" class="bg-white border border-gray-300 rounded-md shadow-sm px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                ← Dashboard
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Vue d'ensemble hebdomadaire -->
    <div class="grid grid-cols-1 lg:grid-cols-7 gap-6 mb-8">
        @php
            $days = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
            $current = $startDate->copy();
        @endphp

        @for($i = 0; $i < 7; $i++)
            @php
                $dayKey = $current->format('Y-m-d');
                $daySeances = $seances_by_day[$dayKey] ?? [];
                $isToday = $current->isToday();
                $isPast = $current->isPast() && !$current->isToday();
            @endphp

            <div class="bg-white overflow-hidden shadow rounded-lg {{ $isToday ? 'ring-2 ring-blue-500' : '' }}">
                <div class="px-4 py-5 sm:p-6">
                    <div class="text-center">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">
                            {{ $current->locale('fr')->isoFormat('dddd') }}
                        </div>
                        <div class="mt-1 text-3xl font-semibold {{ $isToday ? 'text-blue-600' : ($isPast ? 'text-gray-400' : 'text-gray-900') }}">
                            {{ $current->format('d') }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $current->locale('fr')->isoFormat('MMM') }}
                        </div>

                        @if($isToday)
                            <span class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Aujourd'hui
                            </span>
                        @endif

                        <!-- Statistiques du jour -->
                        <div class="mt-4 space-y-2">
                            <div class="text-sm">
                                <span class="font-medium text-gray-900">{{ count($daySeances) }}</span>
                                <span class="text-gray-500">séance(s)</span>
                            </div>

                            @if(count($daySeances) > 0)
                                @php
                                    $uniqueFilms = array_unique(array_column($daySeances, 'film_titre'));
                                @endphp
                                <div class="text-xs text-gray-500">
                                    {{ count($uniqueFilms) }} film(s)
                                </div>
                            @endif
                        </div>

                        @if(count($daySeances) > 0)
                            <div class="mt-3">
                                <a href="{{ route('employee.seances.today') }}?date={{ $current->format('Y-m-d') }}"
                                   class="text-xs font-medium text-blue-600 hover:text-blue-500">
                                    Voir le détail
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @php $current->addDay(); @endphp
        @endfor
    </div>

    <!-- Planning détaillé par jour -->
    <div class="space-y-8">
        @php $current = $startDate->copy(); @endphp

        @for($i = 0; $i < 7; $i++)
            @php
                $dayKey = $current->format('Y-m-d');
                $daySeances = $seances_by_day[$dayKey] ?? [];
                $isToday = $current->isToday();
                $isPast = $current->isPast() && !$current->isToday();
            @endphp

            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 {{ $isToday ? 'bg-blue-50' : '' }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg leading-6 font-medium {{ $isToday ? 'text-blue-900' : 'text-gray-900' }}">
                                {{ $current->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                @if($isToday)
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Aujourd'hui
                                    </span>
                                @endif
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                {{ count($daySeances) }} séance(s) programmée(s)
                            </p>
                        </div>

                        @if(count($daySeances) > 0)
                            <div class="text-sm text-gray-500">
                                @php
                                    $dayRevenue = 0;
                                    foreach($daySeances as $seance) {
                                        $dayRevenue += $seance['revenue'] ?? 0;
                                    }
                                @endphp
                                {{ number_format($dayRevenue, 0) }}€ de CA estimé
                            </div>
                        @endif
                    </div>
                </div>

                @if(count($daySeances) > 0)
                    <div class="border-t border-gray-200">
                        <div class="divide-y divide-gray-200">
                            @foreach($daySeances as $seance)
                                <div class="px-4 py-4 hover:bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <!-- Heure -->
                                            <div class="flex-shrink-0 text-center">
                                                <div class="text-lg font-bold {{ $isPast ? 'text-gray-400' : 'text-gray-900' }}">
                                                    {{ $seance['date_heure_debut']->format('H:i') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $seance['film_duree'] }}min
                                                </div>
                                            </div>

                                            <!-- Film et détails -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center space-x-2">
                                                    <h4 class="text-base font-medium {{ $isPast ? 'text-gray-400' : 'text-gray-900' }} truncate">
                                                        {{ $seance['film_titre'] }}
                                                    </h4>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
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
                                                    <span>💺 {{ $seance['capacite_salle'] ?? 100 }} places</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Métriques rapides -->
                                        <div class="flex items-center space-x-4 text-sm">
                                            <div class="text-center">
                                                <div class="font-medium text-gray-900">{{ $seance['reservations_count'] ?? 0 }}</div>
                                                <div class="text-gray-500">rés.</div>
                                            </div>
                                            <div class="text-center">
                                                <div class="font-medium {{ ($seance['occupancy_rate'] ?? 0) >= 80 ? 'text-green-600' : 'text-gray-900' }}">
                                                    {{ number_format($seance['occupancy_rate'] ?? 0, 0) }}%
                                                </div>
                                                <div class="text-gray-500">occ.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="border-t border-gray-200 px-4 py-8">
                        <div class="text-center">
                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Aucune séance programmée</p>
                        </div>
                    </div>
                @endif
            </div>

            @php $current->addDay(); @endphp
        @endfor
    </div>

    <!-- Statistiques hebdomadaires -->
    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                📊 Statistiques hebdomadaires
            </h3>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900">{{ $total_seances }}</div>
                    <div class="text-sm text-gray-500">Séances total</div>
                </div>
                <div class="text-center">
                    @php
                        $allFilms = [];
                        foreach($seances_by_day as $daySeances) {
                            foreach($daySeances as $seance) {
                                $allFilms[] = $seance['film_titre'];
                            }
                        }
                        $uniqueFilms = array_unique($allFilms);
                    @endphp
                    <div class="text-2xl font-bold text-gray-900">{{ count($uniqueFilms) }}</div>
                    <div class="text-sm text-gray-500">Films différents</div>
                </div>
                <div class="text-center">
                    @php
                        $avgPerDay = $total_seances > 0 ? round($total_seances / 7, 1) : 0;
                    @endphp
                    <div class="text-2xl font-bold text-gray-900">{{ $avgPerDay }}</div>
                    <div class="text-sm text-gray-500">Séances/jour (moy.)</div>
                </div>
                <div class="text-center">
                    @php
                        $totalRevenue = 0;
                        foreach($seances_by_day as $daySeances) {
                            foreach($daySeances as $seance) {
                                $totalRevenue += $seance['revenue'] ?? 0;
                            }
                        }
                    @endphp
                    <div class="text-2xl font-bold text-gray-900">{{ number_format($totalRevenue, 0) }}€</div>
                    <div class="text-sm text-gray-500">CA estimé</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="mt-8 flex justify-center space-x-4">
        <a href="{{ route('employee.seances.today') }}" class="bg-blue-600 border border-transparent rounded-md shadow-sm px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            📅 Séances d'aujourd'hui
        </a>
        <a href="{{ route('employee.reservations.today') }}" class="bg-green-600 border border-transparent rounded-md shadow-sm px-4 py-2 text-sm font-medium text-white hover:bg-green-700">
            🎫 Réservations du jour
        </a>
    </div>
</div>
@endsection