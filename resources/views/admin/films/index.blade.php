@extends('layouts.admin')

@section('title', 'Gestion des Films')

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Films</span>
    </nav>
@endsection

@section('subtitle', 'Gérez le catalogue de films et leurs informations')

@section('actions')
    <x-button href="{{ route('admin.films.create') }}" theme="admin" icon="heroicon-o-plus">
        Nouveau film
    </x-button>
@endsection

@section('content')
    <!-- Search and Filters -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 sm:p-6 mb-6">
        <form method="GET" action="{{ route('admin.films.index') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Titre, réalisateur..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="genre" class="block text-sm font-medium text-gray-700 mb-1">Genre</label>
                <select id="genre" name="genre" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tous les genres</option>
                    @foreach(\App\Domain\Enums\GenreFilm::options() as $value => $label)
                        <option value="{{ $value }}" {{ request('genre') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="classification" class="block text-sm font-medium text-gray-700 mb-1">Classification</label>
                <select id="classification" name="classification" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Toutes</option>
                    @foreach(\App\Domain\Cinema\Enums\ClassificationFilmEnum::options() as $value => $label)
                        <option value="{{ $value }}" {{ request('classification') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select id="statut" name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Actif</option>
                    <option value="inactif" {{ request('statut') === 'inactif' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Filtrer
                </button>
                @if(request()->hasAny(['search', 'genre', 'classification', 'statut']))
                    <a href="{{ route('admin.films.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300">
                        Effacer
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Films Table -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                Liste des Films
                <span class="text-sm font-normal text-gray-500">({{ $total }} total)</span>
            </h2>
        </div>

        @if(count($films) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Film</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Réalisateur</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Genre</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Durée</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Classification</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Statut</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($films as $film)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($film->afficheUrl)
                                        <img class="h-10 w-10 rounded-lg object-cover" src="{{ $film->afficheUrl }}" alt="{{ $film->titre }}">
                                    @else
                                        <div class="h-10 w-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 16h4m10 0h4"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $film->titre ?? 'Titre non défini' }}
                                    </div>
                                    @if($film->titreFr && $film->titreFr !== $film->titre)
                                        <div class="text-sm text-gray-500">
                                            {{ $film->titreFr }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if(is_array($film->realisateurs) && count($film->realisateurs) > 0)
                                    {{ implode(', ', array_slice($film->realisateurs, 0, 2)) }}
                                    @if(count($film->realisateurs) > 2)
                                        <div class="text-xs text-gray-500">+{{ count($film->realisateurs) - 2 }} autre(s)</div>
                                    @endif
                                @else
                                    N/A
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                @if(is_array($film->genres) && count($film->genres) > 0)
                                    {{ $film->genres[0] }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $film->dureeFormatted ?? ($film->dureeMinutes ? $film->dureeMinutes . ' min' : 'N/A') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($film->classification ?? 'TOUS_PUBLICS')
                                @case('TOUS_PUBLICS')
                                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="size-1.5 inline-block rounded-full bg-green-800"></span>
                                        Tous publics
                                    </span>
                                    @break
                                @case('AVERTISSEMENT')
                                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <span class="size-1.5 inline-block rounded-full bg-yellow-800"></span>
                                        Avertissement
                                    </span>
                                    @break
                                @case('MOINS_12')
                                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <span class="size-1.5 inline-block rounded-full bg-orange-800"></span>
                                        Moins 12 ans
                                    </span>
                                    @break
                                @case('MOINS_16')
                                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <span class="size-1.5 inline-block rounded-full bg-red-800"></span>
                                        Moins 16 ans
                                    </span>
                                    @break
                                @case('MOINS_18')
                                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <span class="size-1.5 inline-block rounded-full bg-red-800"></span>
                                        -18 ans
                                    </span>
                                    @break
                                @default
                                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <span class="size-1.5 inline-block rounded-full bg-gray-800"></span>
                                        {{ $film->classification }}
                                    </span>
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($film->estActif ?? true)
                                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="size-1.5 inline-block rounded-full bg-green-800"></span>
                                    Actif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <span class="size-1.5 inline-block rounded-full bg-gray-800"></span>
                                    Inactif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="relative inline-block text-left">
                                <div>
                                    <button type="button"
                                            class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                            onclick="toggleDropdown('dropdown-{{ $film->uuid }}')">
                                        Actions
                                        <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>

                                <div id="dropdown-{{ $film->uuid }}"
                                     class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden">
                                    <div class="py-1">
                                        <a href="{{ route('admin.films.show', $film->uuid) }}"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Voir le détail
                                        </a>
                                        <a href="{{ route('admin.seances.index', ['film_id' => $film->uuid]) }}"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            Gérer les séances
                                        </a>
                                        <a href="{{ route('admin.seances.create', ['film_id' => $film->uuid]) }}"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Nouvelle séance
                                        </a>
                                        <div class="border-t border-gray-100"></div>
                                        <a href="{{ route('admin.films.edit', $film->uuid) }}"
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="mr-3 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Modifier le film
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pagination->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $pagination->links() }}
        </div>
        @endif

        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 16h4m10 0h4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun film</h3>
            <p class="mt-1 text-sm text-gray-500">Commencez par créer un nouveau film.</p>
            <div class="mt-6">
                <x-button href="{{ route('admin.films.create') }}" theme="admin" icon="heroicon-o-plus">
                    Nouveau film
                </x-button>
            </div>
        </div>
        @endif
    </div>

    <script>
        function toggleDropdown(dropdownId) {
            const dropdown = document.getElementById(dropdownId);
            const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');

            // Fermer tous les autres dropdowns
            allDropdowns.forEach(dd => {
                if (dd.id !== dropdownId) {
                    dd.classList.add('hidden');
                }
            });

            // Toggle le dropdown cliqué
            dropdown.classList.toggle('hidden');
        }

        // Fermer les dropdowns en cliquant ailleurs
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.relative')) {
                document.querySelectorAll('[id^="dropdown-"]').forEach(dd => {
                    dd.classList.add('hidden');
                });
            }
        });
    </script>
@endsection
