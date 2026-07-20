@extends('layouts.employee')

@section('title', 'Séances du jour')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    🎭 Séances du {{ $dateJour }}
</h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Stats rapides -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                        <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 16h4m10 0h4" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $seances->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                        <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Terminées</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $seances->filter(fn($s) => $s->date_heure_fin < now())->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                        <svg class="h-5 w-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">En cours</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $seances->filter(fn($s) => $s->date_heure_debut <= now() && $s->date_heure_fin > now())->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                        <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">À venir</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $seances->filter(fn($s) => $s->date_heure_debut > now())->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-4">
                <form method="GET" class="flex flex-wrap gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Salle</label>
                        <select name="salle" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">Toutes les salles</option>
                            @foreach($sallesDisponibles as $salle)
                                <option value="{{ $salle }}" @selected(request('salle') == $salle)>{{ $salle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Film</label>
                        <select name="film" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">Tous les films</option>
                            @foreach($filmsDisponibles as $film)
                                <option value="{{ $film }}" @selected(request('film') == $film)>{{ $film }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="statut" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">Tous</option>
                            <option value="a_venir" @selected(request('statut') == 'a_venir')>À venir</option>
                            <option value="en_cours" @selected(request('statut') == 'en_cours')>En cours</option>
                            <option value="termine" @selected(request('statut') == 'termine')>Terminées</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                            Filtrer
                        </button>
                        @if(request()->anyFilled(['salle', 'film', 'statut']))
                            <a href="{{ route('employee.seances.index') }}" class="ml-2 bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-400">
                                Réinitialiser
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des séances -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Planning des séances - {{ $dateJour }}
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Toutes les séances programmées pour aujourd'hui
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Séance
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Film
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Salle
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Réservations
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($seances as $seance)
                        @php
                            $now = now();
                            $isStarted = $seance->date_heure_debut <= $now;
                            $isFinished = $seance->date_heure_fin <= $now;
                            $totalReservations = $seance->reservations->sum('nombre_places');
                            $capaciteSalle = $seance->salle?->capacite ?? 0;
                            $tauxRemplissage = $capaciteSalle > 0 ? round(($totalReservations / $capaciteSalle) * 100) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $seance->date_heure_debut->format('H:i') }} - {{ $seance->date_heure_fin->format('H:i') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $seance->version }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $seance->film?->titre }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $seance->film?->duree_minutes }}min
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $seance->salle?->nom }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $capaciteSalle }} places
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $totalReservations }} / {{ $capaciteSalle }}
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                    <div class="h-2 rounded-full
                                        @if($tauxRemplissage >= 90)
                                            bg-red-500
                                        @elseif($tauxRemplissage >= 70)
                                            bg-yellow-500
                                        @else
                                            bg-green-500
                                        @endif"
                                        style="width: {{ $tauxRemplissage }}%">
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $tauxRemplissage }}% remplie
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($isFinished)
                                        bg-gray-100 text-gray-800
                                    @elseif($isStarted)
                                        bg-green-100 text-green-800
                                    @elseif($seance->date_heure_debut->diffInMinutes($now) <= 30 && !$isStarted)
                                        bg-yellow-100 text-yellow-800
                                    @else
                                        bg-blue-100 text-blue-800
                                    @endif">
                                    @if($isFinished)
                                        Terminée
                                    @elseif($isStarted)
                                        En cours
                                    @elseif($seance->date_heure_debut->diffInMinutes($now) <= 30)
                                        Bientôt
                                    @else
                                        Programmée
                                    @endif
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 16h4m10 0h4" />
                                    </svg>
                                    <h3 class="text-sm font-medium text-gray-900">Aucune séance</h3>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Aucune séance programmée pour aujourd'hui.
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($seances->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $seances->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection