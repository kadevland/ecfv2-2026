<header class="sticky top-0 z-40 w-full border-b border-cinema-gold/10 backdrop-blur-lg bg-cinema-black/90"
    :class="{ 'shadow-2xl': scrolled }">
    <x-container size="full">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-cinema-gold rounded-lg flex items-center justify-center">
                        <x-brand-logo size="sm" color="black" />
                    </div>
                    <span class="text-xl font-bold font-serif text-cinema-gold">Cinéphoria</span>
                </a>
            </div>

            <!-- Navigation Desktop -->
            <nav class="hidden lg:flex items-center space-x-8">
                <x-link href="{{ route('films.index') }}" theme="cinema" color="white"
                    class="hover:text-cinema-gold transition-colors">
                    Films
                </x-link>
                @php
                    /*    <x-link href="{{ route('reservation.index') }}" theme="cinema" color="white" class="hover:text-cinema-gold transition-colors">
                    Réservation
                </x-link>*/
                @endphp
                <x-link href="{{ route('seances.index') }}" theme="cinema" color="white"
                    class="hover:text-cinema-gold transition-colors">
                    Séances
                </x-link>
                <x-link href="{{ route('cinemas.index') }}" theme="cinema" color="white"
                    class="hover:text-cinema-gold transition-colors">
                    Cinémas
                </x-link>
                @php
                /*
                <x-link href="{{ route('account') }}" theme="cinema" color="white"
                    class="hover:text-cinema-gold transition-colors">
                    Mon Compte
                </x-link>*/
                @endphp
            </nav>

            <!-- Actions -->
            <div class="flex items-center space-x-4">
                <!-- Search -->
                <button data-hs-overlay="#search-modal"
                    class="p-2 text-gray-300 hover:text-cinema-gold transition-colors" aria-label="Ouvrir la recherche">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>


                <!-- User Menu -->
                <div class="hidden md:flex items-center">
                    <x-cinema.top-menu-user />
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="lg:hidden p-2 text-gray-300 hover:text-cinema-gold transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </x-container>
</header>
