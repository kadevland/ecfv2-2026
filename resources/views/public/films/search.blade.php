@extends('layouts.cinema')

@section('title', 'Recherche : ' . $search)

@section('content')
    <!-- Header de recherche -->
    <div class="bg-black text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gold mb-4">Résultats de recherche</h1>
                <p class="text-xl text-gray-300 mb-6">
                    {{ $resultCount }} résultat(s) pour "<span class="text-gold">{{ $search }}</span>"
                </p>

                <!-- Nouvelle recherche -->
                <div class="max-w-md mx-auto">
                    <form action="{{ route('films.search') }}" method="GET" class="relative">
                        <input type="text"
                               name="q"
                               value="{{ $search }}"
                               placeholder="Affiner votre recherche..."
                               class="w-full px-4 py-3 pl-12 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-gold">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <span class="bg-gold text-black px-4 py-2 rounded-r-lg font-medium hover:bg-gold/90 transition-colors">
                                Rechercher
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation de contexte -->
    <div class="bg-gray-50 border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center gap-2 text-sm">
                <a href="{{ route('films.index') }}" class="text-gray-600 hover:text-gray-900">Films</a>
                <span class="text-gray-400">›</span>
                <span class="text-gray-900 font-medium">Recherche</span>
            </nav>
        </div>
    </div>

    <!-- Résultats -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($films->count() > 0)
            <!-- Actions rapides -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <span class="text-gray-600">{{ $films->total() }} film(s) trouvé(s)</span>
                    @if($films->hasPages())
                        <span class="text-gray-400">
                            Page {{ $films->currentPage() }} sur {{ $films->lastPage() }}
                        </span>
                    @endif
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('films.index') }}"
                       class="text-gold hover:text-gold/80 font-medium">
                        Voir tous les films
                    </a>
                </div>
            </div>

            <!-- Grille des résultats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($films as $film)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
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
                                    {{ $film->classification }}
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

                            <!-- Indicateur de correspondance -->
                            <div class="absolute bottom-2 left-2">
                                @php
                                    $searchTerms = explode(' ', strtolower($search));
                                    $titleMatches = 0;
                                    $descriptionMatches = 0;

                                    foreach($searchTerms as $term) {
                                        if(str_contains(strtolower($film->titre), $term)) $titleMatches++;
                                        if(str_contains(strtolower($film->description ?? ''), $term)) $descriptionMatches++;
                                    }

                                    $matchType = $titleMatches > 0 ? 'titre' : ($descriptionMatches > 0 ? 'description' : 'autre');
                                @endphp

                                @if($matchType === 'titre')
                                    <span class="bg-green-500/90 text-white px-2 py-1 rounded text-xs font-medium">
                                        Titre
                                    </span>
                                @elseif($matchType === 'description')
                                    <span class="bg-blue-500/90 text-white px-2 py-1 rounded text-xs font-medium">
                                        Synopsis
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Informations du film -->
                        <div class="p-4">
                            <h3 class="font-bold text-lg text-gray-900 mb-2 line-clamp-2">
                                <a href="{{ route('films.show', $film->film_id) }}" class="hover:text-gold transition-colors">
                                    {!! preg_replace('/('.preg_quote($search, '/').')/i', '<mark class="bg-yellow-200">$1</mark>', $film->titre) !!}
                                </a>
                            </h3>

                            <div class="space-y-2 text-sm text-gray-600">
                                <p><span class="font-medium">Genre :</span> {{ ucfirst($film->genre) }}</p>
                                <p><span class="font-medium">Durée :</span> {{ $film->duree }} min</p>

                                @if($film->realisateur)
                                    <p><span class="font-medium">Réalisateur :</span> {{ $film->realisateur }}</p>
                                @endif

                                @if($film->nombre_avis > 0)
                                    <p class="text-xs text-gray-500">{{ $film->nombre_avis }} avis</p>
                                @endif
                            </div>

                            @if($film->description)
                                <p class="text-sm text-gray-700 mt-3 line-clamp-3">
                                    {!! preg_replace('/('.preg_quote($search, '/').')/i', '<mark class="bg-yellow-200">$1</mark>', $film->description) !!}
                                </p>
                            @endif

                            <!-- Actions -->
                            <div class="mt-4 flex gap-2">
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
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun résultat trouvé</h3>
                <p class="text-gray-500 mb-6">
                    Nous n'avons trouvé aucun film correspondant à "<span class="font-medium">{{ $search }}</span>"
                </p>

                <!-- Suggestions -->
                <div class="bg-gray-50 rounded-lg p-6 max-w-md mx-auto">
                    <h4 class="font-medium text-gray-900 mb-3">Suggestions :</h4>
                    <ul class="text-sm text-gray-600 space-y-1 text-left">
                        <li>• Vérifiez l'orthographe de votre recherche</li>
                        <li>• Essayez avec des mots-clés plus généraux</li>
                        <li>• Recherchez par genre ou réalisateur</li>
                        <li>• Parcourez le catalogue complet</li>
                    </ul>
                </div>

                <div class="mt-6 flex gap-3 justify-center">
                    <a href="{{ route('films.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gold text-black rounded-md font-medium hover:bg-gold/90 transition-colors">
                        Voir tous les films
                    </a>
                    <button onclick="document.querySelector('input[name=q]').focus()"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                        Nouvelle recherche
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Recherches suggérées -->
    @if($films->count() > 0)
        <div class="bg-gray-100 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recherches suggérées</h3>
                <div class="flex flex-wrap gap-2">
                    @php
                        $suggestions = ['action', 'comédie', 'drame', 'horreur', 'science-fiction'];
                    @endphp
                    @foreach($suggestions as $suggestion)
                        <a href="{{ route('films.search', ['q' => $suggestion]) }}"
                           class="px-3 py-1 bg-white border border-gray-300 rounded-full text-sm text-gray-700 hover:border-gold hover:text-gold transition-colors">
                            {{ ucfirst($suggestion) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
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

.hover\:text-gold\/80:hover {
    color: rgba(212, 175, 55, 0.8);
}

.hover\:border-gold:hover {
    border-color: var(--gold);
}

.focus\:border-gold:focus {
    border-color: var(--gold);
}

mark {
    background-color: rgb(254 240 138);
    padding: 0 2px;
    border-radius: 2px;
}
</style>
@endpush