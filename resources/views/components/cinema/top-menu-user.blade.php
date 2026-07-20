{{--
    Cinema Top Menu User Component

    Menu utilisateur pour le thème cinema avec gestion des états connecté/non-connecté

    Usage:
    <x-cinema.top-menu-user :is-connected="false" />
    <x-cinema.top-menu-user :is-connected="true" :menus="$customMenus" />
--}}

@if($isConnected)
    {{-- Utilisateur connecté --}}
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open"
                class="flex items-center space-x-3 p-2 rounded-md hover:bg-gray-900/50 focus:outline-none focus:ring-2 focus:ring-cinema-gold transition-colors">

            {{-- Avatar Gravatar basé sur l'email --}}
            @php
                $gravatarHash = md5(strtolower(trim($email ?? '')));
                $gravatarUrl = "https://www.gravatar.com/avatar/{$gravatarHash}?s=32&d=identicon&r=pg";
            @endphp
            <img src="{{ $gravatarUrl }}"
                 alt="{{ $fullname }}"
                 class="w-8 h-8 rounded-full border border-cinema-gold/30"
                 loading="lazy">

            <div class="hidden md:block text-left">
                <div class="text-sm font-medium text-white">
                    @php
                        // Format: Prénom + première lettre du nom
                        $nameParts = explode(' ', trim($fullname));
                        $firstName = $nameParts[0] ?? '';
                        $lastInitial = isset($nameParts[1]) ? substr($nameParts[1], 0, 1) . '.' : '';
                        $displayName = trim($firstName . ' ' . $lastInitial);
                    @endphp
                    {{ $displayName ?: $fullname }}
                </div>
                <div class="text-xs text-gray-400">Mon Compte</div>
            </div>

            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                 :class="{ 'rotate-180': open }"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        {{-- Menu dropdown utilisateur connecté --}}
        <div x-show="open"
             @click.away="open = false"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute right-0 mt-2 w-56 bg-gray-800/95 border border-cinema-gold/20 rounded-lg shadow-xl py-2 z-50">

            {{-- En-tête du menu --}}
            <div class="px-4 py-3 border-b border-cinema-gold/20">
                <p class="text-sm font-medium text-cinema-gold">{{ $fullname }}</p>
                <p class="text-xs text-gray-400">{{ $email }}</p>
            </div>

            {{-- Items du menu --}}
            @foreach ($menus as $menu)
                <a href="{{ $menu['link'] }}"
                   class="flex items-center px-4 py-2 text-sm text-gray-300 hover:text-cinema-gold hover:bg-gray-900/50 transition-colors">
                    @if (isset($menu['icon']))
                        <x-icon :name="$menu['icon']" class="w-4 h-4 mr-3 text-gray-400" />
                    @endif
                    {{ $menu['label'] }}
                </a>
            @endforeach

            {{-- Divider --}}
            <x-divider theme="cinema" color="cinema-gold/20" />

            {{-- Déconnexion --}}
            <form method="POST" action="/logout" class="w-full">
                @csrf
                <button type="submit"
                        class="w-full flex items-center px-4 py-2 text-sm text-cinema-red-light hover:text-cinema-red hover:bg-gray-900/50 transition-colors text-left">
                    <x-heroicon-o-arrow-left-on-rectangle class="w-4 h-4 mr-3" />
                    Se déconnecter
                </button>
            </form>
        </div>
    </div>

@else
    {{-- Utilisateur non connecté --}}
    <div class="flex items-center space-x-3">
        {{-- Bouton Connexion --}}
        <a href="/login"
           class="text-sm text-gray-300 hover:text-cinema-gold transition-colors font-medium">
            Connexion
        </a>

        {{-- Séparateur --}}
        <div class="h-4 w-px bg-gray-600"></div>

        {{-- Bouton Inscription --}}
        <a href="/register"
           class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-black bg-cinema-gold rounded-md hover:bg-cinema-gold/90 transition-colors">
            Inscription
        </a>
    </div>
@endif
