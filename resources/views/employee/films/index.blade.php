@extends('layouts.employee')

@section('title', 'Films du jour')

@php
use App\Domain\Enums\ClassificationFilm;
@endphp

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    🎬 Films du {{ $dateJour }}
</h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Filtre par classification -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
            <div class="px-4 py-4">
                <form method="GET" class="flex flex-wrap gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Classification</label>
                        <select name="classification" class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">Toutes les classifications</option>
                            @foreach($classificationsDisponibles as $value => $label)
                                <option value="{{ $value }}" @selected(request('classification') == $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">
                            Filtrer
                        </button>
                        @if(request()->filled('classification'))
                            <a href="{{ route('employee.films.index') }}" class="ml-2 bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-400">
                                Réinitialiser
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des films -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Films programmés
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    {{ $filmsAvecSeances->count() }} film(s) au programme aujourd'hui
                </p>
            </div>

            <div class="divide-y divide-gray-200">
                @forelse ($filmsAvecSeances as $filmData)
                <div class="px-6 py-6">
                    <div class="flex items-start space-x-4">
                        <!-- Affiche du film (placeholder) -->
                        <div class="flex-shrink-0">
                            <div class="h-24 w-16 bg-gray-200 rounded-md flex items-center justify-center">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16l5.5-5 5.5 5V4z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Informations du film -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold text-gray-900">
                                        {{ $filmData['film']->titre }}
                                    </h4>
                                    <div class="mt-1 flex items-center space-x-4 text-sm text-gray-600">
                                        <span>🕐 {{ $filmData['film']->duree_minutes ?? 'N/A' }} min</span>
                                        @php
                                            $classification = ClassificationFilm::tryFrom($filmData['film']->classification ?? '');
                                        @endphp
                                        <span class="{{ $classification?->getColorClass() ?? 'text-gray-600' }}">
                                            🔞 {{ $classification?->label() ?? 'Non classé' }}
                                        </span>
                                        <span>🎭 {{ is_array($filmData['film']->genres ?? null) ? implode(', ', $filmData['film']->genres) : 'Genre non spécifié' }}</span>
                                    </div>
                                    @if($filmData['film']->synopsis)
                                        <p class="mt-2 text-sm text-gray-700 line-clamp-2">
                                            {{ Str::limit($filmData['film']->synopsis, 150) }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Statistiques du film -->
                                <div class="ml-4 text-right">
                                    <div class="text-2xl font-bold text-blue-600">{{ $filmData['nb_seances'] }}</div>
                                    <div class="text-xs text-gray-500">séances</div>
                                    <div class="mt-2 text-xs text-gray-600">
                                        {{ $filmData['premiere_seance']->format('H:i') }} -
                                        {{ $filmData['derniere_seance']->format('H:i') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Séances du film -->
                            <div class="mt-4">
                                <h5 class="text-sm font-medium text-gray-900 mb-2">Séances programmées :</h5>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($filmData['seances'] as $seance)
                                        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                            @if($seance->date_heure_debut < now())
                                                bg-gray-100 text-gray-700
                                            @elseif($seance->date_heure_debut < now()->addHour())
                                                bg-yellow-100 text-yellow-800
                                            @else
                                                bg-green-100 text-green-800
                                            @endif">
                                            {{ $seance->date_heure_debut->format('H:i') }} -
                                            Salle {{ $seance->salle->nom ?? 'N/A' }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-12 text-center">
                    <div class="text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16l5.5-5 5.5 5V4z" />
                        </svg>
                        <h3 class="text-sm font-medium text-gray-900">Aucun film</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            @if(request()->filled('classification'))
                                Aucun film avec cette classification n'est programmé aujourd'hui.
                            @else
                                Aucun film n'est programmé aujourd'hui.
                            @endif
                        </p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection