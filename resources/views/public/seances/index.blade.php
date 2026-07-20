@extends('layouts.cinema')

@section('title', 'Toutes les séances')

@section('content')
    <!-- Header -->
    <div class="bg-black text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gold mb-4 flex justify-center items-end"><x-bi-film class="w-12 h-12 mr-2 inline-block"/>Toutes les séances</h1>
                <p class="text-xl text-gray-300">
                    Découvrez toutes les séances disponibles dans nos cinémas
                </p>
                @if($totalSeances > 0)
                    <p class="mt-2 text-gray-400">
                        {{ $totalSeances }} séance(s) disponible(s)
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-gray-900 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <form method="GET" action="{{ route('seances.index') }}" class="flex flex-wrap items-center gap-4">
                <!-- Filtre Date -->
                <div class="flex items-center gap-2">
                    <label for="date" class="text-sm font-medium text-gray-300">Date :</label>
                    <input type="date"
                           name="date"
                           id="date"
                           value="{{ $filters['date'] ?? '' }}"
                           min="{{ now()->format('Y-m-d') }}"
                           max="{{ now()->addDays(30)->format('Y-m-d') }}"
                           class="px-3 py-2 bg-gray-800 border border-gray-700 rounded-md text-sm text-white focus:ring-2 focus:ring-gold focus:border-gold">
                </div>

                <!-- Filtre Cinéma -->
                <div class="flex items-center gap-2">
                    <label for="cinema" class="text-sm font-medium text-gray-300">Cinéma :</label>
                    <select name="cinema" id="cinema"
                            class="px-3 py-2 bg-gray-800 border border-gray-700 rounded-md text-sm text-white focus:ring-2 focus:ring-gold focus:border-gold">
                        <option value="">Tous les cinémas</option>
                        @foreach($cinemasDisponibles as $cinemaId => $nomCinema)
                            <option value="{{ $cinemaId }}"
                                    @if(($filters['cinema'] ?? '') == $cinemaId) selected @endif>
                                {{ $nomCinema }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtre Genre -->
                <div class="flex items-center gap-2">
                    <label for="genre" class="text-sm font-medium text-gray-300">Genre :</label>
                    <select name="genre" id="genre"
                            class="px-3 py-2 bg-gray-800 border border-gray-700 rounded-md text-sm text-white focus:ring-2 focus:ring-gold focus:border-gold">
                        <option value="">Tous les genres</option>
                        @foreach($genresDisponibles as $genre)
                            <option value="{{ $genre }}"
                                    @if(($filters['genre'] ?? '') == $genre) selected @endif>
                                {{ ucfirst($genre) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                        class="px-4 py-2 bg-gold text-black rounded-md hover:bg-gold/90 transition-colors font-medium">
                    Filtrer
                </button>

                @if(array_filter($filters))
                    <a href="{{ route('seances.index') }}"
                       class="px-4 py-2 border border-gray-600 text-gray-300 rounded-md hover:bg-gray-800 transition-colors">
                        Réinitialiser
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Liste des séances -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($seances->count() > 0)
            <div class="space-y-8">
                @foreach($seances as $date => $filmsOfDay)
                    <!-- Section par date -->
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <!-- En-tête de date -->
                        <div class="bg-black px-6 py-4">
                            <h2 class="text-xl font-bold text-gold">
                                {{ \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                @if(\Carbon\Carbon::parse($date)->isToday())
                                    <span class="ml-2 text-sm text-gray-300">(Aujourd'hui)</span>
                                @elseif(\Carbon\Carbon::parse($date)->isTomorrow())
                                    <span class="ml-2 text-sm text-gray-300">(Demain)</span>
                                @endif
                            </h2>
                        </div>

                        <!-- Films de cette date -->
                        <div class="divide-y divide-gray-200">
                            @foreach($filmsOfDay as $filmData)
                                <div class="p-6">
                                    <div class="flex gap-6">
                                        <!-- Affiche du film -->
                                        <div class="flex-shrink-0 w-24 h-36 bg-gray-200 rounded overflow-hidden">
                                            @if($filmData->film->affiche)
                                                <img src="{{ $filmData->film->affiche }}"
                                                     alt="{{ $filmData->film->titre }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-gray-800">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 16h4m10 0h4"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Informations du film -->
                                        <div class="flex-1">
                                            <div class="flex items-start justify-between mb-4">
                                                <div>
                                                    <h3 class="text-xl font-semibold text-gray-900">
                                                        <a href="{{ route('films.show', $filmData->film->id) }}"
                                                           class="hover:text-gold transition-colors">
                                                            {{ $filmData->film->titre }}
                                                        </a>
                                                    </h3>
                                                    <div class="mt-1 flex items-center gap-4 text-sm text-gray-600">
                                                        @if($filmData->film->duree)
                                                            <span>{{ $filmData->film->duree }} min</span>
                                                        @endif
                                                        @if($filmData->film->genre)
                                                            <span>{{ ucfirst($filmData->film->genre) }}</span>
                                                        @endif
                                                        <x-classification-badge :value="$filmData->film->classification" />
                                                    </div>
                                                </div>
                                                <a href="{{ route('films.seances', $filmData->film->id) }}"
                                                   class="text-cinema-red-light hover:text-gold/80 text-sm font-medium">
                                                    Toutes les séances →
                                                </a>
                                            </div>

                                            <!-- Séances groupées par cinéma -->
                                            <div class="space-y-4">
                                                @foreach($filmData->seances as $cinemaId => $seancesCinema)
                                                    <div class="border-l-2 border-gold pl-4">
                                                        <h4 class="font-medium text-gray-900 mb-2">
                                                            {{ $seancesCinema->first()->cinema_nom }}
                                                        </h4>
                                                        <div class="flex flex-wrap gap-2">
                                                            @foreach($seancesCinema as $seance)
                                                                <a href="{{ route('seance.reserver', $seance->seance_id) }}"
                                                                   class="group relative">
                                                                    <div class="px-3 py-2 border rounded-lg transition-all
                                                                                {{ $seance->places_disponibles > 0
                                                                                   ? 'border-gray-300 hover:border-gold hover:shadow-md cursor-pointer'
                                                                                   : 'border-gray-200 bg-gray-100 cursor-not-allowed' }}">
                                                                        <div class="flex items-center gap-2">
                                                                            <span class="font-semibold {{ $seance->places_disponibles > 0 ? 'text-gray-900' : 'text-gray-400' }}">
                                                                                {{ $seance->date_heure_debut->format('H:i') }}
                                                                            </span>
                                                                            @if($seance->version !== 'VF')
                                                                                <span class="text-xs px-1 py-0.5 bg-gray-200 text-gray-700 rounded">
                                                                                    {{ $seance->version }}
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                        <div class="mt-1 text-xs {{ $seance->places_disponibles > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                                            @if($seance->places_disponibles > 0)
                                                                                {{ $seance->places_disponibles }} places
                                                                            @else
                                                                                Complet
                                                                            @endif
                                                                        </div>
                                                                    </div>

                                                                    <!-- Tooltip avec plus d'infos -->
                                                                    @if($seance->places_disponibles > 0)
                                                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-black text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap">
                                                                            <div>Salle: {{ $seance->salle_nom }}</div>
                                                                            <div>Qualité: {{ $seance->qualite_projection }}</div>
                                                                            <div>À partir de {{ number_format($seance->tarif_base, 2) }}€</div>
                                                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-black"></div>
                                                                        </div>
                                                                    @endif
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($pagination->hasPages())
                <div class="mt-8">
                    {{ $pagination->links() }}
                </div>
            @endif
        @else
            <!-- État vide -->
            <div class="text-center py-16">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 16h4m10 0h4"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune séance trouvée</h3>
                <p class="text-gray-500 mb-6">
                    @if(array_filter($filters))
                        Essayez de modifier vos critères de filtrage pour voir plus de séances.
                    @else
                        Aucune séance n'est programmée pour le moment. Revenez bientôt !
                    @endif
                </p>
                @if(array_filter($filters))
                    <a href="{{ route('seances.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gold text-black rounded-md font-medium hover:bg-gold/90 transition-colors">
                        Voir toutes les séances
                    </a>
                @else
                    <a href="{{ route('films.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gold text-black rounded-md font-medium hover:bg-gold/90 transition-colors">
                        Découvrir nos films
                    </a>
                @endif
            </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
:root {
    --gold: #d4af37;
}

.text-gold {
    color: var(--gold);
}

.bg-gold {
    background-color: var(--gold);
}

.border-gold {
    border-color: var(--gold);
}

.bg-gold\/90 {
    background-color: rgba(212, 175, 55, 0.9);
}

.hover\:bg-gold:hover {
    background-color: var(--gold);
}

.hover\:bg-gold\/90:hover {
    background-color: rgba(212, 175, 55, 0.9);
}

.hover\:bg-gold\/80:hover {
    background-color: rgba(212, 175, 55, 0.8);
}

.hover\:text-gold:hover {
    color: var(--gold);
}

.hover\:text-gold\/80:hover {
    color: rgba(212, 175, 55, 0.8);
}

.hover\:border-gold:hover {
    border-color: var(--gold);
}

.focus\:ring-gold:focus {
    --tw-ring-color: var(--gold);
}

.focus\:border-gold:focus {
    border-color: var(--gold);
}
</style>
@endpush

@push('scripts')
<script>
// Auto-submit du formulaire quand on change un filtre
document.querySelectorAll('#date, #cinema, #genre').forEach(element => {
    element.addEventListener('change', function() {
        this.closest('form').submit();
    });
});
</script>
@endpush
