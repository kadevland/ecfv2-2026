@extends('layouts.cinema')

@section('title', 'Catalogue des Films')

@section('content')

<div class="bg-black min-h-screen"><!-- Background sombre pour toute la page -->
    <!-- Hero Section -->
    <div class="bg-black text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gold mb-4 flex justify-center items-end"><x-maki-cinema class="w-12 h-12 mr-2 inline-block"/> Catalogue des Films</h1>
                <p class="text-xl text-gray-300">{{ $films->total() }} films disponibles</p>
            </div>
        </div>
    </div>

    <!-- Filtres et Tri -->
    <div class="bg-gray-900 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <form method="GET" action="{{ route('films.index') }}" class="flex flex-wrap items-center gap-4">
                <!-- Filtre Genre -->
                <div class="flex items-center gap-2">
                    <label for="genre" class="text-sm font-medium text-gray-300">Genre :</label>
                    <select name="genre" id="genre"
                            class="px-3 py-2 bg-gray-800 border border-gray-700 text-white rounded-md text-sm focus:ring-2 focus:ring-gold focus:border-gold">
                        <option value="">Tous les genres</option>
                        @foreach($genres as $value => $label)
                            <option value="{{ $value }}"
                                    @if(($filters['genre'] ?? '') === $value) selected @endif>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtre Classification -->
                <div class="flex items-center gap-2">
                    <label for="classification" class="text-sm font-medium text-gray-300">Classification :</label>
                    <select name="classification" id="classification"
                            class="px-3 py-2 bg-gray-800 border border-gray-700 text-white rounded-md text-sm focus:ring-2 focus:ring-gold focus:border-gold">
                        <option value="">Toutes</option>
                        @foreach($classifications as $value => $label)
                            <option value="{{ $value }}"
                                    @if(($filters['classification'] ?? '') === $value) selected @endif>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- <!-- Tri -->
                <div class="flex items-center gap-2">
                    <label for="sort" class="text-sm font-medium text-gray-300">Trier par :</label>
                    <select name="sort" id="sort"
                            class="px-3 py-2 bg-gray-800 border border-gray-700 text-white rounded-md text-sm focus:ring-2 focus:ring-gold focus:border-gold">
                        <option value="popular" @if($currentSort === 'popular') selected @endif>Popularité</option>
                        <option value="recent" @if($currentSort === 'recent') selected @endif>Plus récents</option>
                    </select>
                </div> --}}

                <button type="submit"
                        class="px-4 py-2 bg-gold text-black rounded-md hover:bg-gold/90 transition-colors font-medium">
                    Filtrer
                </button>

                @if(array_filter($filters))
                    <a href="{{ route('films.index') }}"
                       class="px-4 py-2 border border-gray-600 text-gray-300 rounded-md hover:bg-gray-800 transition-colors">
                        Réinitialiser
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Liste des Films -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($films->count() > 0)
            <div class="mb-6">
                <p class="text-gray-400">{{ $films->total() }} film(s) trouvé(s)</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($films as $film)
                    <div class="bg-gray-900 border border-gray-800 rounded-lg overflow-hidden hover:border-gold transition-colors flex flex-col h-full">
                        <!-- Affiche du film -->
                        <div class="relative aspect-[2/3] bg-gray-200">
                            @if($film->affiche_url)
                                <img src="{{ $film->affiche_url }}"
                                     alt="{{ $film->titre }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-300">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif

                            <!-- Badge classification -->
                            <div class="absolute top-2 left-2">
                                <span class="bg-black/75 text-white px-2 py-1 rounded text-xs font-medium">
                                    {{ $classifications[$film->classification] ?? $film->classification }}
                                </span>
                            </div>

                            <!-- Note -->
                            @if($film->note_moyenne)
                                <div class="absolute top-2 right-2">
                                    <div class="bg-gold/90 text-black px-2 py-1 rounded flex items-center text-sm font-bold">
                                        ⭐ {{ number_format($film->note_moyenne, 1) }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Informations du film -->
                        <div class="p-4 flex-1 flex flex-col">
                            <h3 class="font-bold text-lg text-gold mb-2 line-clamp-2">
                                <a href="{{ route('films.show', $film->film_id) }}" class="hover:text-yellow-400 transition-colors">
                                    {{ $film->titre }}
                                </a>
                            </h3>

                            <div class="space-y-2 text-sm text-gray-300">
                                <p><span class="font-medium">Genre :</span> {{ ucfirst($film->genre) }}</p>
                                <p><span class="font-medium">Durée :</span> {{ $film->duree }} min</p>

                                @if($film->nombre_avis > 0)
                                    <p class="text-xs text-gray-500">{{ $film->nombre_avis }} avis</p>
                                @endif
                            </div>

                            @if($film->description)
                                <p class="text-sm text-gray-400 mt-3 line-clamp-3">{{ $film->description }}</p>
                            @endif

                            <!-- Actions -->
                            <div class="mt-auto pt-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('films.show', $film->film_id) }}"
                                       class="flex-1 bg-gold text-black text-center py-2 px-4 rounded font-medium hover:bg-gold/90 transition-colors">
                                        Voir détails
                                    </a>
                                    <a href="{{ route('films.seances', $film->film_id) }}"
                                       class="flex-1 border border-gold text-gold text-center py-2 px-4 rounded font-medium hover:bg-gold hover:text-black transition-colors">
                                        Séances
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($films->hasPages())
                <div class="mt-12">
                    {{ $films->links() }}
                </div>
            @endif
        @else
            <!-- État vide -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 011 1v1a1 1 0 01-1 1v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7a1 1 0 01-1-1V5a1 1 0 011-1h4zM9 3v1h6V3H9z"/>
                </svg>
                <h3 class="text-lg font-medium text-white mb-2">Aucun film trouvé</h3>
                <p class="text-gray-400 mb-4">Essayez de modifier vos critères de recherche</p>
                <a href="{{ route('films.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gold text-black rounded-md font-medium hover:bg-gold/90 transition-colors">
                    Voir tous les films
                </a>
            </div>
        @endif
    </div>
</div><!-- Fin du background sombre -->
@endsection

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

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

.focus\:ring-gold:focus {
    --tw-ring-color: var(--gold);
}

.focus\:border-gold:focus {
    border-color: var(--gold);
}
</style>
@endpush
