<!DOCTYPE html>
<html lang="fr" class="h-full cinema">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Cinéphoria') }} - @yield('title', 'Votre cinéma de référence')</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="h-full bg-cinema-black text-white font-sans antialiased cinema-theme"
      x-data="{
          cartOpen: false,
          mobileMenuOpen: false,
          scrolled: false
      }"
      x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })">

    <!-- Header -->
    @include('layouts.partials.cinema.header')

    <!-- Hero Section (optionnel) -->
    @yield('hero')

    <!-- Main Content -->
    <main class="relative">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('layouts.partials.cinema.footer')

    <!-- Search Modal -->
    <x-search-modal />

    <!-- Mobile Menu -->
    @include('layouts.partials.cinema.mobile-menu')

    <!-- Back to Top Component -->
    <x-back-top-page theme="cinema"
                     :show-progress="true"
                     threshold="200"
                     tooltip-text="Retour en haut" />

    <!-- Search functionality -->
    {{-- <script>
        // Search functionality for cinema layout
        function performSearch(query) {
            const resultsContainer = document.getElementById('search-results');

            if (!query || query.length < 2) {
                resultsContainer.innerHTML = `
                    <div class="text-gray-400 text-sm text-center py-8">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Tapez pour rechercher parmi notre catalogue de films...
                    </div>
                `;
                return;
            }

            // Show loading state
            resultsContainer.innerHTML = `
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-cinema-gold mx-auto mb-4"></div>
                    <p class="text-gray-400 text-sm">Recherche en cours...</p>
                </div>
            `;

            // Simulate search with setTimeout (replace with actual search API)
            setTimeout(() => {
                const mockResults = [
                    { title: 'Dune: Part Two', genre: 'Science-Fiction', year: '2024', rating: '8.5' },
                    { title: 'Oppenheimer', genre: 'Drame, Histoire', year: '2023', rating: '8.7' },
                    { title: 'Barbie', genre: 'Comédie, Fantaisie', year: '2023', rating: '7.2' }
                ].filter(movie =>
                    movie.title.toLowerCase().includes(query.toLowerCase()) ||
                    movie.genre.toLowerCase().includes(query.toLowerCase())
                );

                if (mockResults.length === 0) {
                    resultsContainer.innerHTML = `
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-400 text-sm">Aucun résultat pour "${query}"</p>
                            <p class="text-gray-500 text-xs mt-2">Essayez avec d'autres mots-clés</p>
                        </div>
                    `;
                } else {
                    const resultsHTML = mockResults.map(movie => `
                        <div class="flex items-center justify-between p-3 bg-gray-900/30 rounded-lg hover:bg-gray-900/50 transition-colors cursor-pointer">
                            <div class="flex-1">
                                <h4 class="text-white font-medium">${movie.title}</h4>
                                <p class="text-gray-400 text-sm">${movie.genre} • ${movie.year}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-cinema-gold text-sm font-medium">★ ${movie.rating}</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    `).join('');

                    resultsContainer.innerHTML = `
                        <div class="space-y-2">
                            <p class="text-gray-400 text-sm mb-3">${mockResults.length} résultat(s) trouvé(s) :</p>
                            ${resultsHTML}
                        </div>
                    `;
                }
            }, 500);
        }

        // Focus search input when modal opens
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('search-modal');
            if (modal) {
                modal.addEventListener('hs.overlay.open', function() {
                    setTimeout(() => {
                        const searchInput = document.getElementById('search-input');
                        if (searchInput) {
                            searchInput.focus();
                        }
                    }, 100);
                });
            }
        });
    </script> --}}

    @stack('scripts')
</body>
</html>
