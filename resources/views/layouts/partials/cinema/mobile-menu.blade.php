<!-- Mobile Menu Overlay -->
<div x-show="mobileMenuOpen"
     x-cloak
     @keydown.escape="mobileMenuOpen = false"
     class="fixed inset-0 z-50 lg:hidden">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-cinema-black/90 backdrop-blur-sm"
         @click="mobileMenuOpen = false"></div>

    <!-- Menu Panel -->
    <div class="fixed inset-y-0 right-0 w-full max-w-sm bg-gradient-to-b from-cinema-black via-cinema-black to-cinema-black/95 border-l border-cinema-gold/20"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full">

        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-cinema-gold/20">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-cinema-gold rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-cinema-black" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18 3v2h-2V3H8v2H6V3H4v18h2v-2h2v2h8v-2h2v2h2V3h-2zM8 17H6v-2h2v2zm0-4H6v-2h2v2zm0-4H6V7h2v2zm10 8h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V7h2v2z"/>
                    </svg>
                </div>
                <span class="text-lg font-bold font-serif text-cinema-gold">Menu</span>
            </div>
            <button @click="mobileMenuOpen = false"
                    class="p-2 text-gray-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- User Section -->
        <div class="p-4 border-b border-cinema-gold/20">
            {{-- Version mobile du menu utilisateur --}}
            <div class="flex items-center justify-center w-full">
                <x-cinema.top-menu-user />
            </div>
        </div>

        <!-- Navigation -->
        <nav class="p-4 space-y-2">
            <a href="{{ route('films.index') }}" class="flex items-center space-x-3 p-3 rounded-lg text-white hover:bg-cinema-gold/10 hover:text-cinema-gold transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v2h4a1 1 0 0 1 0 2h-1v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6H3a1 1 0 0 1 0-2h4zM9 3v1h6V3H9zm2 8a1 1 0 0 1 2 0v6a1 1 0 0 1-2 0v-6z"></path>
                </svg>
                <span>Films</span>
            </a>

            <a href="{{ route('films.index') }}" class="flex items-center space-x-3 p-3 rounded-lg text-white hover:bg-cinema-gold/10 hover:text-cinema-gold transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v4h4a1 1 0 0 1 0 2h-1v11a3 3 0 0 1-3 3H8a3 3 0 0 1-3-3V9H4a1 1 0 0 1 0-2h4z"></path>
                </svg>
                <span>Séances</span>
            </a>

            <a href="{{ route('cinemas.index') }}" class="flex items-center space-x-3 p-3 rounded-lg text-white hover:bg-cinema-gold/10 hover:text-cinema-gold transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span>Nos Cinémas</span>
            </a>

            <a href="#" class="flex items-center space-x-3 p-3 rounded-lg text-white hover:bg-cinema-gold/10 hover:text-cinema-gold transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <span>Promotions</span>
            </a>
        </nav>

        <x-divider theme="cinema" color="cinema-gold" class="opacity-20 mx-4" />

        <!-- Account Links -->
        <div class="p-4 space-y-2">
            <a href="#" class="flex items-center space-x-3 p-3 rounded-lg text-gray-300 hover:bg-gray-900/50 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Mon compte</span>
            </a>

            <a href="{{ route('account') }}" class="flex items-center space-x-3 p-3 rounded-lg text-gray-300 hover:bg-gray-900/50 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <span>Mes réservations</span>
            </a>

            <a href="#" class="flex items-center space-x-3 p-3 rounded-lg text-gray-300 hover:bg-gray-900/50 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <span>Mes favoris</span>
            </a>

            <a href="#" class="flex items-center space-x-3 p-3 rounded-lg text-cinema-red hover:bg-cinema-red/10 hover:text-cinema-red-light transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span>Déconnexion</span>
            </a>
        </div>

        <!-- Cart Summary -->
        <div class="p-4 border-t border-cinema-gold/20">
            <div class="bg-cinema-gold/10 rounded-lg p-3">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-white">Panier</span>
                    <x-badge color="warning" theme="cinema" size="xs">3 articles</x-badge>
                </div>
                <div class="text-xs text-gray-400 mb-3">2 billets + 1 menu</div>
                <x-button variant="solid" color="primary" theme="cinema" size="sm" class="w-full">
                    Voir le panier
                </x-button>
            </div>
        </div>
    </div>
</div>