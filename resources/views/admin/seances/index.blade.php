@extends('layouts.admin')

@section('title', 'Gestion des Séances')

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Séances</span>
    </nav>
@endsection

@section('subtitle', 'Gérez les séances de cinéma et leurs horaires')

{{-- @section('actions')
    <x-button href="{{ route('admin.seances.create') }}" theme="admin" icon="heroicon-o-plus">
        Nouvelle séance
    </x-button>
@endsection --}}

@section('content')
    <!-- Search and Filters -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 sm:p-6 mb-6">
        <form method="GET" action="{{ route('admin.seances.index') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <div>
                <label for="film_id" class="block text-sm font-medium text-gray-700 mb-1">Film</label>
                <select id="film_id" name="film_id" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tous les films</option>
                    @foreach($films as $film)
                        <option value="{{ $film['id'] }}" {{ request('film_id') === $film['id'] ? 'selected' : '' }}>
                            {{ $film['titre'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="salle_id" class="block text-sm font-medium text-gray-700 mb-1">Salle</label>
                <select id="salle_id" name="salle_id" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Toutes les salles</option>
                    @foreach($salles as $salle)
                        <option value="{{ $salle['id'] }}" {{ request('salle_id') === $salle['id'] ? 'selected' : '' }}>
                            {{ $salle['display_name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" id="date_debut" name="date_debut" value="{{ request('date_debut') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select id="statut" name="statut" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tous les statuts</option>
                    @foreach(\App\Domain\Enums\StatutSeance::cases() as $statutCase)
                        <option value="{{ $statutCase->value }}" {{ request('statut') === $statutCase->value ? 'selected' : '' }}>
                            {{ $statutCase->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Filtrer
                </button>
                @if(request()->hasAny(['film_id', 'salle_id', 'date_debut', 'statut']))
                    <a href="{{ route('admin.seances.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300">
                        Effacer
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Seances Table -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                Liste des Séances
                <span class="text-sm font-normal text-gray-500">({{ $total }} total)</span>
            </h2>
        </div>

        @if(count($seances) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Film & Salle</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Horaires</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Version</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Tarification</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Statut</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($seances as $seance)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $seance->filmTitre ?? 'Film non trouvé' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $seance->salleDisplayName ?? 'Salle non trouvée' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $seance->dateHeureDebut ? \Carbon\Carbon::parse($seance->dateHeureDebut)->format('d/m/Y à H:i') : 'N/A' }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $seance->dateHeureFin ? \Carbon\Carbon::parse($seance->dateHeureFin)->format('H:i') : 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $seance->version ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ number_format($seance->prixMin, 2) }}€ - {{ number_format($seance->prixMax, 2) }}€
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $seance->placesDisponibles }}/{{ $seance->placesTotales }} places
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($seance->statut ?? 'programmee')
                                @case('programmee')
                                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <span class="size-1.5 inline-block rounded-full bg-yellow-800"></span>
                                        Programmée
                                    </span>
                                    @break
                                @case('en_cours')
                                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <span class="size-1.5 inline-block rounded-full bg-blue-800"></span>
                                        En cours
                                    </span>
                                    @break
                                @case('terminee')
                                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="size-1.5 inline-block rounded-full bg-green-800"></span>
                                        Terminée
                                    </span>
                                    @break
                                @case('annulee')
                                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <span class="size-1.5 inline-block rounded-full bg-red-800"></span>
                                        Annulée
                                    </span>
                                    @break
                                @default
                                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <span class="size-1.5 inline-block rounded-full bg-gray-800"></span>
                                        {{ $seance->statut }}
                                    </span>
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                    Actions
                                    <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <div class="py-1">
                                        <a href="{{ route('admin.seances.show', $seance->uuid) }}" class="flex px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Voir les détails
                                        </a>
                                        <a href="{{ route('admin.seances.edit', $seance->uuid) }}" class="flex px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                            Modifier
                                        </a>
                                        <button type="button" class="flex w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50" onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette séance ?')) { /* TODO: Delete action */ }">
                                            <svg class="mr-3 h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                            Supprimer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination Laravel -->
        @if($pagination->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $pagination->appends(request()->query())->links() }}
        </div>
        @endif

        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune séance</h3>
            {{-- <p class="mt-1 text-sm text-gray-500">Commencez par créer une nouvelle séance.</p>
            <div class="mt-6">
                <x-button href="{{ route('admin.seances.create') }}" theme="admin" icon="heroicon-o-plus">
                    Nouvelle séance
                </x-button>
            </div>
        </div> --}}
        @endif
    </div>
@endsection
