@extends('layouts.cinema')

@section('title', 'Accueil')

@push('scripts')
<script>
// Keyboard shortcut for search modal
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        const searchModal = document.getElementById('search-modal');
        if (searchModal) {
            window.HSOverlay.open(searchModal);
        }
    }
});
</script>
@endpush

@section('content')
    <!-- Hero Section Amélioré -->
    <section class="relative bg-gradient-to-b from-gray-900 to-black py-20 lg:py-32">
        <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-transparent"></div>
        <div class="absolute inset-0">
            <img class="w-full h-full object-cover" src="https://picsum.photos/1980/800/?blur=2&grayscale" alt="Cinema Background">
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-5xl md:text-7xl font-bold text-white mb-6">
                    Bienvenue chez <span class="text-cinema-gold">Cinéphoria</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-200 mb-4 max-w-3xl mx-auto">
                    Découvrez l'expérience cinématographique ultime dans nos 7 salles premium en France et Belgique
                </p>

                <!-- Stats temps réel -->
                <div class="flex justify-center gap-8 mb-8 text-center">
                    <div>
                        <div class="text-3xl font-bold text-cinema-gold" id="films-count">{{ $stats['total_films'] ?? 0 }}</div>
                        <div class="text-sm text-gray-300">Films à l'affiche</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-cinema-gold" id="genres-count">{{ $stats['genres_count'] ?? 0 }}</div>
                        <div class="text-sm text-gray-300">Genres disponibles</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-cinema-gold" id="avg-rating">{{ number_format($stats['avg_rating'] ?? 0, 1) }}</div>
                        <div class="text-sm text-gray-300">Note moyenne</div>
                    </div>
                </div>

                <!-- Search Section Améliorée -->
                <div class="max-w-2xl mx-auto mb-8">
                    <button type="button"
                            class="w-full px-6 py-4 bg-gray-800/50 border border-gray-700 rounded-lg text-gray-400 text-lg focus:outline-none hover:border-cinema-gold backdrop-blur transition-all flex items-center justify-between"
                            data-hs-overlay="#search-modal">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Rechercher un film, un genre, un acteur...
                        </div>
                        <span class="text-sm bg-gray-700 px-2 py-1 rounded">Ctrl+K</span>
                    </button>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('films.index') }}" class="inline-flex items-center justify-center px-8 py-4 bg-cinema-gold text-black text-lg font-semibold rounded-lg hover:bg-yellow-600 transition">
                        Découvrir nos films
                    </a>
                    <a href="{{ route('cinemas.index') }}" class="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-white text-lg font-semibold rounded-lg hover:bg-white hover:text-black transition">
                        Nos cinémas
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Films de la semaine -->
    <x-films-grid
        :films="$currentWeekFilms"
        title="Films de cette"
        titleAccent="semaine"
        :showAllUrl="route('films.index')" />

    <!-- Films à venir -->
    <x-films-grid
        :films="$upcomingFilms"
        title="Films à"
        titleAccent="venir"
        :showAllUrl="route('films.index')" />

    <!-- Films acclamés -->
    @if($topRatedFilms->isNotEmpty())
    <div class="bg-gray-900/50">
        <x-films-grid
            :films="$topRatedFilms"
            title="Films"
            titleAccent="Acclamés"
            :showAll="false" />
    </div>
    @endif

    <!-- Services Section (Inchangée) -->
    <section class="py-16 bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">
                    L'expérience <span class="text-cinema-gold">Cinéphoria</span>
                </h2>
                <p class="text-lg text-gray-400 max-w-2xl mx-auto">
                    Découvrez ce qui fait de Cinéphoria l'expérience cinéma la plus premium d'Europe
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Service 1 -->
                <div class="text-center group">
                    <div class="w-16 h-16 bg-cinema-gold rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-black" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">Technologie IMAX</h3>
                    <p class="text-gray-400">
                        Vivez vos films préférés avec une qualité d'image et de son exceptionnelle grâce à nos écrans IMAX dernière génération.
                    </p>
                </div>

                <!-- Service 2 -->
                <div class="text-center group">
                    <div class="w-16 h-16 bg-cinema-gold rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-black" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">Sièges Premium</h3>
                    <p class="text-gray-400">
                        Détendez-vous dans nos fauteuils en cuir inclinables avec espace VIP pour un confort optimal pendant toute la séance.
                    </p>
                </div>

                <!-- Service 3 -->
                <div class="text-center group">
                    <div class="w-16 h-16 bg-cinema-gold rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-black" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">Restauration Gourmande</h3>
                    <p class="text-gray-400">
                        Savourez nos spécialités gastronomiques et nos cocktails signature dans nos bars lounge avant ou après la séance.
                    </p>
                </div>
            </div>
        </div>
    </section>
    {{--
    <!-- Newsletter (Inchangée) -->
    <section class="py-16 bg-gradient-to-r from-gray-900 to-gray-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">
                Restez informé des <span class="text-cinema-gold">nouveautés</span>
            </h2>
            <p class="text-lg text-gray-300 mb-8">
                Recevez en avant-première les informations sur nos films, événements spéciaux et offres exclusives
            </p>

            <form class="max-w-md mx-auto">
                <div class="flex gap-4">
                    <input type="email" class="flex-1 py-3 px-4 bg-gray-800 border-gray-700 rounded-lg text-white focus:border-cinema-gold focus:ring-cinema-gold" placeholder="Votre adresse email">
                    <button type="submit" class="py-3 px-6 bg-cinema-gold text-black font-semibold rounded-lg hover:bg-yellow-600 transition">
                        S'abonner
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-3">
                    En vous abonnant, vous acceptez de recevoir nos emails marketing. Vous pouvez vous désabonner à tout moment.
                </p>
            </form>
        </div>
    </section>
    --}}
@endsection
