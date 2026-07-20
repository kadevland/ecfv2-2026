@extends('layouts.cinema')

@section('title', 'Films ' . $genreDisplay)

@section('content')
    <!-- Header Genre -->
    <div class="bg-black text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <nav class="text-sm text-gray-300 mb-4">
                    <a href="{{ route('films.index') }}" class="hover:text-white">Films</a>
                    <span class="mx-2">›</span>
                    <span class="text-gold">{{ $genreDisplay }}</span>
                </nav>
                <h1 class="text-4xl font-bold text-gold mb-4">🎬 Films {{ $genreDisplay }}</h1>
                <p class="text-xl text-gray-300">
                    Découvrez notre sélection de films {{ strtolower($genreDisplay) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Navigation des genres -->
    <div class="bg-gray-50 border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-medium text-gray-700 mr-3">Autres genres :</span>
                @php
                    $genres = ['action', 'aventure', 'comédie', 'drame', 'fantaisie', 'horreur', 'romance', 'science-fiction', 'thriller'];
                @endphp
                @foreach($genres as $genreOption)
                    @if(strtolower($genreOption) !== strtolower($genre))
                        <a href="{{ route('films.by-genre', strtolower($genreOption)) }}"
                           class="px-3 py-1 bg-white border border-gray-300 rounded-full text-sm text-gray-700 hover:border-gold hover:text-gold transition-colors">
                            {{ ucfirst($genreOption) }}
                        </a>
                    @else
                        <span class="px-3 py-1 bg-gold text-black rounded-full text-sm font-medium">
                            {{ ucfirst($genreOption) }}
                        </span>
                    @endif
                @endforeach
                <a href="{{ route('films.index') }}"
                   class="px-3 py-1 bg-gray-700 text-white rounded-full text-sm hover:bg-gray-600 transition-colors ml-2">
                    Tous les genres
                </a>
            </div>
        </div>
    </div>

    <!-- Liste des films -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($films->count() > 0)
            <div class="mb-6">
                <p class="text-gray-600">{{ $films->total() }} film(s) {{ strtolower($genreDisplay) }} en diffusion</p>
            </div>

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

                            <!-- Badge genre -->
                            <div class="absolute top-2 right-2">
                                @if($film->note_moyenne)
                                    <div class="bg-gold/90 text-black px-2 py-1 rounded flex items-center text-sm font-bold mb-1">
                                        ⭐ {{ number_format($film->note_moyenne, 1) }}
                                    </div>
                                @endif
                                <div class="bg-gold/90 text-black px-2 py-1 rounded text-xs font-medium">
                                    {{ ucfirst($film->genre) }}
                                </div>
                            </div>
                        </div>

                        <!-- Informations du film -->
                        <div class="p-4">
                            <h3 class="font-bold text-lg text-gray-900 mb-2 line-clamp-2">
                                <a href="{{ route('films.show', $film->film_id) }}" class="hover:text-gold transition-colors">
                                    {{ $film->titre }}
                                </a>
                            </h3>

                            <div class="space-y-2 text-sm text-gray-600">
                                <p><span class="font-medium">Durée :</span> {{ $film->duree }} min</p>

                                @if($film->realisateur)
                                    <p><span class="font-medium">Réalisateur :</span> {{ $film->realisateur }}</p>
                                @endif

                                @if($film->date_sortie)
                                    <p><span class="font-medium">Sortie :</span> {{ $film->date_sortie->format('Y') }}</p>
                                @endif

                                @if($film->nombre_avis > 0)
                                    <p class="text-xs text-gray-500">{{ $film->nombre_avis }} avis</p>
                                @endif
                            </div>

                            @if($film->description)
                                <p class="text-sm text-gray-700 mt-3 line-clamp-3">{{ $film->description }}</p>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 011 1v1a1 1 0 01-1 1v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7a1 1 0 01-1-1V5a1 1 0 011-1h4zM9 3v1h6V3H9z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun film {{ strtolower($genreDisplay) }} en diffusion</h3>
                <p class="text-gray-500 mb-6">
                    Nous n'avons actuellement aucun film {{ strtolower($genreDisplay) }} en cours de diffusion.
                </p>

                <div class="flex gap-3 justify-center">
                    <a href="{{ route('films.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-gold text-black rounded-md font-medium hover:bg-gold/90 transition-colors">
                        Voir tous les films
                    </a>
                </div>

                <!-- Suggestions d'autres genres -->
                <div class="mt-8 bg-gray-50 rounded-lg p-6 max-w-md mx-auto">
                    <h4 class="font-medium text-gray-900 mb-3">Explorez d'autres genres :</h4>
                    <div class="flex flex-wrap gap-2 justify-center">
                        @foreach(['action', 'comédie', 'drame', 'thriller'] as $genreSuggestion)
                            @if(strtolower($genreSuggestion) !== strtolower($genre))
                                <a href="{{ route('films.by-genre', strtolower($genreSuggestion)) }}"
                                   class="px-3 py-1 bg-white border border-gray-300 rounded-full text-sm text-gray-700 hover:border-gold hover:text-gold transition-colors">
                                    {{ ucfirst($genreSuggestion) }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Section recommandations -->
    @if($films->count() > 0)
        <div class="bg-gray-100 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Vous aimez les films {{ strtolower($genreDisplay) }} ?</h2>
                    <p class="text-gray-600">Découvrez également ces genres qui pourraient vous plaire</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @php
                        $genresSuggestes = [
                            'action' => ['aventure', 'thriller', 'science-fiction'],
                            'aventure' => ['action', 'fantaisie', 'science-fiction'],
                            'comédie' => ['romance', 'drame', 'aventure'],
                            'drame' => ['romance', 'thriller', 'comédie'],
                            'fantaisie' => ['aventure', 'science-fiction', 'action'],
                            'horreur' => ['thriller', 'science-fiction', 'fantaisie'],
                            'romance' => ['comédie', 'drame', 'aventure'],
                            'science-fiction' => ['action', 'aventure', 'thriller'],
                            'thriller' => ['action', 'horreur', 'drame'],
                        ];
                        $suggestions = $genresSuggestes[strtolower($genre)] ?? ['action', 'comédie', 'drame'];
                    @endphp

                    @foreach($suggestions as $genreSuggere)
                        <a href="{{ route('films.by-genre', $genreSuggere) }}"
                           class="bg-white border border-gray-200 rounded-lg p-4 text-center hover:border-gold hover:shadow-md transition-all group">
                            <div class="text-2xl mb-2">
                                @switch($genreSuggere)
                                    @case('action') 💥 @break
                                    @case('aventure') 🗺️ @break
                                    @case('comédie') 😄 @break
                                    @case('drame') 🎭 @break
                                    @case('fantaisie') 🔮 @break
                                    @case('horreur') 👻 @break
                                    @case('romance') 💕 @break
                                    @case('science-fiction') 🚀 @break
                                    @case('thriller') ⚡ @break
                                    @default 🎬
                                @endswitch
                            </div>
                            <h3 class="font-medium text-gray-900 group-hover:text-gold transition-colors">
                                {{ ucfirst($genreSuggere) }}
                            </h3>
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

.hover\:border-gold:hover {
    border-color: var(--gold);
}
</style>
@endpush