<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard Employé') - Cinéphoria</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="h-full bg-gray-50 font-sans antialiased">
    <div class="min-h-full">
        <!-- Navigation -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo et nav principale -->
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('employee.dashboard') }}" class="text-2xl font-bold text-gray-900">
                                🎬 <span class="text-orange-600">Cinéphoria</span>
                            </a>
                            <span class="ml-3 px-2 py-1 bg-orange-100 text-orange-800 text-sm font-medium rounded">
                                Employé
                            </span>
                        </div>

                        <!-- Navigation principale -->
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <x-nav-link :href="route('employee.dashboard')" :active="request()->routeIs('employee.dashboard')">
                                📊 Dashboard
                            </x-nav-link>

                            <x-nav-link :href="route('employee.seances.index')" :active="request()->routeIs('employee.seances.*')">
                                🎭 Séances
                            </x-nav-link>

                            <x-nav-link :href="route('employee.reservations.index')" :active="request()->routeIs('employee.reservations.*')">
                                🎫 Réservations
                            </x-nav-link>

                            <x-nav-link :href="route('employee.films.index')" :active="request()->routeIs('employee.films.*')">
                                🎬 Films
                            </x-nav-link>

                        </div>
                    </div>

                    <!-- Infos employé et actions -->
                    <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-4">
                        <!-- Cinéma actuel -->
                        <div class="text-sm bg-gray-100 px-3 py-1 rounded-full">
                            📍 {{ $cinema_name ?? 'Cinéphoria Centre-Ville' }}
                        </div>

                        <!-- Menu utilisateur -->
                        <div class="relative group">
                            <button class="bg-gray-800 flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                <span class="sr-only">Open user menu</span>
                                @php
                                    $user = Auth::user();
                                    $email = $user->credential->email ?? 'user@example.com';
                                    $gravatarHash = md5(strtolower(trim($email)));
                                    $gravatarUrl = "https://www.gravatar.com/avatar/{$gravatarHash}?s=32&d=identicon&r=pg";
                                @endphp
                                <img src="{{ $gravatarUrl }}"
                                     alt="Avatar"
                                     class="h-8 w-8 rounded-full border border-orange-300"
                                     loading="lazy">
                            </button>

                            <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="py-1">
                                    <a href="{{ route('home') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🏠 Site public
                                    </a>
                                    <div class="border-t border-gray-100"></div>
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            🚪 Déconnexion
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Menu mobile -->
                    <div class="sm:hidden flex items-center">
                        <button type="button" class="bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-orange-500" x-data="{ open: false }" @click="open = !open">
                            <span class="sr-only">Open main menu</span>
                            <svg class="h-6 w-6" x-show="!open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg class="h-6 w-6" x-show="open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- En-tête de page -->
        @hasSection('header')
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                @yield('header')
            </div>
        </header>
        @endif

        <!-- Contenu principal -->
        <main>
            <!-- Messages flash -->
            @if(session('success'))
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-4">
                    <div class="rounded-md bg-green-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-4">
                    <div class="rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Contenu de la page -->
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>