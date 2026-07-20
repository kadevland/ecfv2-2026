@extends('layouts.cinema')

@section('title', 'Séances - ' . $film->titre)

@section('content')
    <!-- Header avec info film -->
    <div class="bg-black text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-6">
                <!-- Mini affiche -->
                <div class="w-16 h-24 bg-gray-800 rounded overflow-hidden flex-shrink-0">
                    @if ($film->affiche_url)
                        <img src="{{ $film->affiche_url }}" alt="{{ $film->titre }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Info film -->
                <div class="flex-1">
                    <nav class="text-sm text-gray-300 mb-2">
                        <a href="{{ route('films.index') }}" class="hover:text-white">Films</a>
                        <span class="mx-2">›</span>
                        <a href="{{ route('films.show', $film->film_id) }}" class="hover:text-white">{{ $film->titre }}</a>
                        <span class="mx-2">›</span>
                        <span class="text-gold">Séances</span>
                    </nav>
                    <h1 class="text-2xl font-bold text-gold mb-1">{{ $film->titre }}</h1>
                    <div class="text-gray-300">
                        {{ $film->duree }} min • {{ ucfirst($film->genre) }} • {{ __($film->classification) }}
                        @if ($film->note_moyenne)
                            • ⭐ {{ number_format($film->note_moyenne, 1) }}
                        @endif
                    </div>
                </div>

                <!-- Action retour -->
                <a href="{{ route('films.show', $film->film_id) }}"
                    class="border border-gold text-gold px-4 py-2 rounded hover:bg-gold hover:text-black transition-colors">
                    ← Retour au film
                </a>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-gray-900 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <form method="GET" action="{{ route('films.seances', $film->film_id) }}"
                class="flex flex-wrap items-center gap-4">
                <!-- Filtre Cinéma -->
                <div class="flex items-center gap-2">
                    <label for="cinema" class="text-sm font-medium text-gray-300">Cinéma :</label>
                    <select name="cinema" id="cinema"
                        class="px-3 py-2 bg-gray-800 border border-gray-700 rounded-md text-sm text-white focus:ring-2 focus:ring-gold focus:border-gold">
                        <option value="">Tous les cinémas</option>
                        @foreach ($cinemasDisponibles as $cinemaId => $nomCinema)
                            <option value="{{ $cinemaId }}" @if (($filters['cinema'] ?? '') == $cinemaId) selected @endif>
                                {{ $nomCinema }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtre Date -->
                <div class="flex items-center gap-2">
                    <label for="date" class="text-sm font-medium text-gray-300">Date :</label>
                    <input type="date" name="date" id="date" value="{{ $filters['date'] ?? '' }}"
                        class="px-3 py-2 bg-gray-800 border border-gray-700 rounded-md text-sm text-white focus:ring-2 focus:ring-gold focus:border-gold">
                </div>

                <button type="submit"
                    class="px-4 py-2 bg-gold text-black rounded-md hover:bg-gold/90 transition-colors font-medium">
                    Filtrer
                </button>

                @if (array_filter($filters))
                    <a href="{{ route('films.seances', $film->film_id) }}"
                        class="px-4 py-2 border border-gray-600 text-gray-300 rounded-md hover:bg-gray-800 transition-colors">
                        Réinitialiser
                    </a>
                @endif
            </form>
        </div>
    </div>
    <!-- Liste des séances -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if ($seances->count() > 0)
            <div class="mb-6">
                <p class="text-gray-600">{{ $totalSeances }} séance(s) trouvée(s)</p>
            </div>

            <div class="space-y-8">
                @foreach ($seances as $date => $seancesParDate)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <!-- En-tête de date -->
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                            </h2>
                        </div>

                        <!-- Séances par cinéma -->
                        <div class="divide-y divide-gray-200">
                            @foreach ($seancesParDate as $cinemaId => $seancesCinema)
                                <div class="p-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900">
                                                {{ $seancesCinema->first()->nom_cinema }}
                                            </h3>
                                            <p class="text-sm text-gray-600">
                                                {{ $seancesCinema->count() }} séance(s) disponible(s)
                                            </p>
                                        </div>
                                        <a href="{{ route('cinemas.show', $cinemaId) }}"
                                            class="text-gold hover:text-gold/80 text-sm font-medium">
                                            Voir le cinéma →
                                        </a>
                                    </div>

                                    <!-- Grille des séances -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                        @foreach ($seancesCinema as $seance)
                                            <div class="border border-gray-200 rounded-lg p-4 hover:border-gold hover:shadow-md transition-all cursor-pointer"
                                                onclick="selectSeance('{{ $seance->seance_id }}')">
                                                <!-- Heure et version -->
                                                <div class="flex items-center justify-between mb-3">
                                                    <span class="text-xl font-bold text-gray-900">
                                                        {{ $seance->date_heure_debut->format('H:i') }}
                                                    </span>
                                                    <span
                                                        class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-sm font-medium">
                                                        {{ $seance->version }}
                                                    </span>
                                                </div>

                                                <!-- Informations séance -->
                                                <div class="space-y-2 text-sm text-gray-600 mb-4">
                                                    <div class="flex justify-between">
                                                        <span>Fin :</span>
                                                        <span>{{ $seance->date_heure_fin->format('H:i') }}</span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span>Salle :</span>
                                                        <span>{{ $seance->nom_salle }}</span>
                                                    </div>
                                                    @if ($seance->qualite_projection)
                                                        <div class="flex justify-between">
                                                            <span>Qualité :</span>
                                                            <span
                                                                class="font-medium">{{ $seance->qualite_projection }}</span>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Places disponibles -->
                                                <div class="mb-4">
                                                    @if ($seance->places_disponibles > 0)
                                                        <div class="flex items-center text-green-600">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            <span
                                                                class="text-sm font-medium">{{ $seance->places_disponibles }}
                                                                places</span>
                                                        </div>
                                                    @else
                                                        <div class="flex items-center text-red-600">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                            <span class="text-sm font-medium">Complet</span>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Tarifs -->
                                                @if ($seance->tarifMin)
                                                    <div class="border-t border-gray-200 pt-3 mb-4">
                                                        <div class="text-xs text-gray-500 mb-1">À partir de</div>
                                                        @php
                                                            $tarifMin = $seance->tarifMin;
                                                        @endphp
                                                        <div class="text-lg font-bold text-gray-900">
                                                            {{ number_format($tarifMin, 2) }} €
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Bouton réservation -->
                                                @if ($seance->places_disponibles > 0)
                                                    <a href="{{ route('seance.reserver', $seance->seance_id) }}"
                                                        class="block w-full bg-gold text-black py-2 px-4 rounded font-medium hover:bg-gold/90 transition-colors text-center">
                                                        Réserver
                                                    </a>
                                                @else
                                                    <button type="button" disabled
                                                        class="w-full bg-gray-200 text-gray-500 py-2 px-4 rounded font-medium cursor-not-allowed">
                                                        Complet
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>



            @if ($seances->hasPages())
                <div class="mt-12">
                    {{ $seances->links() }}
                </div>
            @endif
        @else
            <!-- État vide -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune séance trouvée</h3>
                <p class="text-gray-500 mb-4">
                    @if (array_filter($filters))
                        Essayez de modifier vos critères de filtrage
                    @else
                        Aucune séance n'est programmée pour ce film dans les prochains jours
                    @endif
                </p>
                @if (array_filter($filters))
                    <a href="{{ route('films.seances', $film->film_id) }}"
                        class="inline-flex items-center px-4 py-2 bg-gold text-black rounded-md font-medium hover:bg-gold/90 transition-colors">
                        Voir toutes les séances
                    </a>
                @else
                    <a href="{{ route('films.show', $film->film_id) }}"
                        class="inline-flex items-center px-4 py-2 bg-gold text-black rounded-md font-medium hover:bg-gold/90 transition-colors">
                        Retour au film
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
        function selectSeance(seanceId) {
            console.log('Séance sélectionnée:', seanceId);
            // Logique de sélection de séance
        }

        // Fonction reserverSeance supprimée - liens directs utilisés maintenant
    </script>
@endpush
