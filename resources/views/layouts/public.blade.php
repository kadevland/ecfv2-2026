<!DOCTYPE html>
<html lang="fr" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Cinéphoria') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="h-full bg-white text-gray-900 font-sans antialiased">

    <!-- Navigation -->
    <nav class="bg-black text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gold rounded-lg flex items-center justify-center">
                            <span class="text-black font-bold text-lg">🎬</span>
                        </div>
                        <span class="text-xl font-bold text-gold">Cinéphoria</span>
                    </a>
                </div>

                <!-- Navigation principale -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('films.index') }}"
                       class="text-gray-300 hover:text-gold transition-colors @if(request()->routeIs('films.*')) text-gold @endif">
                        Films
                    </a>
                    <a href="{{ route('cinemas.index') }}"
                       class="text-gray-300 hover:text-gold transition-colors @if(request()->routeIs('cinemas.*')) text-gold @endif">
                        Cinémas
                    </a>
                </div>

                <!-- Actions utilisateur -->
                <div class="flex items-center space-x-4">
                    <!-- Recherche rapide -->
                    <button type="button"
                            class="hidden sm:flex items-center space-x-2 px-3 py-1 bg-gray-800 border border-gray-700 rounded text-gray-400 hover:text-gold hover:border-gold transition-colors"
                            data-hs-overlay="#search-modal">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span class="text-sm">Rechercher...</span>
                    </button>

                    @auth
                        <!-- Utilisateur connecté -->
                        <div class="relative">
                            <button class="text-gray-300 hover:text-gold transition-colors">
                                {{ Auth::user()->name }}
                            </button>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-300 hover:text-gold transition-colors">
                                Déconnexion
                            </button>
                        </form>
                    @else
                        <!-- Utilisateur non connecté -->
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-gold transition-colors">
                            Connexion
                        </a>
                        <a href="{{ route('register') }}"
                           class="bg-gold text-black px-4 py-2 rounded font-medium hover:bg-gold/90 transition-colors">
                            Inscription
                        </a>
                    @endauth
                </div>

                <!-- Menu mobile -->
                <div class="md:hidden">
                    <button type="button" class="text-gray-300 hover:text-gold" onclick="toggleMobileMenu()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Menu mobile -->
            <div id="mobile-menu" class="hidden md:hidden border-t border-gray-800 pt-4 pb-4">
                <div class="space-y-2">
                    <a href="{{ route('films.index') }}"
                       class="block text-gray-300 hover:text-gold transition-colors @if(request()->routeIs('films.*')) text-gold @endif">
                        Films
                    </a>
                    <a href="{{ route('cinemas.index') }}"
                       class="block text-gray-300 hover:text-gold transition-colors @if(request()->routeIs('cinemas.*')) text-gold @endif">
                        Cinémas
                    </a>

                    <!-- Recherche mobile -->
                    <button type="button"
                            class="w-full flex items-center justify-center space-x-2 px-3 py-2 bg-gray-800 border border-gray-700 rounded text-gray-400 hover:text-gold hover:border-gold transition-colors mt-2"
                            data-hs-overlay="#search-modal">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span class="text-sm">Rechercher un film...</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Modal de recherche -->
    <x-search-modal />

    <!-- Footer -->
    <footer class="bg-black text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo et description -->
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-gold rounded-lg flex items-center justify-center">
                            <span class="text-black font-bold text-lg">🎬</span>
                        </div>
                        <span class="text-xl font-bold text-gold">Cinéphoria</span>
                    </div>
                    <p class="text-gray-400 max-w-md">
                        Votre chaîne de cinémas de référence. Découvrez les derniers films,
                        réservez vos séances et vivez des expériences cinématographiques uniques.
                    </p>
                </div>

                <!-- Navigation -->
                <div>
                    <h3 class="text-lg font-semibold text-gold mb-4">Navigation</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('films.index') }}" class="hover:text-gold transition-colors">Films</a></li>
                        <li><a href="{{ route('cinemas.index') }}" class="hover:text-gold transition-colors">Cinémas</a></li>
                        <li><a href="{{ route('films.index') }}" class="hover:text-gold transition-colors">Catalogue</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="text-lg font-semibold text-gold mb-4">Contact</h3>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li>📞 01 23 45 67 89</li>
                        <li>✉️ contact@cinephoria.fr</li>
                        <li>📍 Paris, France</li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400 text-sm">
                <p>&copy; {{ date('Y') }} Cinéphoria. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    @stack('scripts')

    <script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    }

    // Fermer le menu mobile quand on clique ailleurs
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('mobile-menu');
        const button = event.target.closest('button');

        if (!menu.contains(event.target) && (!button || !button.onclick)) {
            menu.classList.add('hidden');
        }
    });
    </script>

    <!-- Styles globaux -->
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

    .focus\:border-gold:focus {
        border-color: var(--gold);
    }
    </style>
</body>

</html>