@extends('layouts.admin')

@section('title', 'Gestion des Salles')

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Salles</span>
    </nav>
@endsection

@section('subtitle', 'Gérez les salles de cinéma et leurs équipements')

@section('actions')
    <x-button href="{{ route('admin.salles.create') }}" theme="admin" icon="heroicon-o-plus">
        Nouvelle salle
    </x-button>
@endsection

@section('content')
    <!-- Search and Filters -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 sm:p-6 mb-6">
        <form method="GET" action="{{ route('admin.salles.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                    <input type="search"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Rechercher par nom de salle..."
                           class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
                </div>

                <div>
                    <label for="cinema_id" class="block text-sm font-medium text-gray-700 mb-1">Cinéma</label>
                    <select id="cinema_id" name="cinema_id" class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous les cinémas</option>
                        @foreach($cinemas as $cinema)
                            <option value="{{ $cinema->id->value }}" {{ request('cinema_id') === $cinema->id->value ? 'selected' : '' }}>
                                {{ $cinema->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select id="statut" name="statut" class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('statut') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="maintenance" {{ request('statut') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="inactive" {{ request('statut') === 'inactive' ? 'selected' : '' }}>Hors service</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700">
                    Rechercher
                </button>
                @if(request()->hasAny(['search', 'cinema_id', 'statut']))
                <a href="{{ route('admin.salles.index') }}"
                   class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 text-gray-500 hover:border-gray-300 hover:text-gray-600 focus:outline-none focus:border-gray-300 focus:text-gray-600">
                    Effacer les filtres
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Salles Table -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                Liste des Salles
                <span class="text-sm font-normal text-gray-500">({{ $total }} total)</span>
            </h2>
        </div>

        @if(count($salles) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Salle</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Capacité</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Configuration</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Équipements</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Statut</th>
                        <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($salles as $salle)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h3a1 1 0 011 1v1a1 1 0 01-1 1v9a2 2 0 01-2 2H6a2 2 0 01-2-2V7a1 1 0 01-1-1V5a1 1 0 011-1h3z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $salle->nom ?? 'Salle sans nom' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $salle->cinemaNom ?? 'Cinéma non défini' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $salle->capaciteTotale ?? 0 }} places</div>
                            <div class="text-sm text-gray-500">
                                {{ $salle->nombreRangees ?? 0 }} rangées × {{ $salle->placesParRangee ?? 0 }} places
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Configuration des sièges</div>
                            <div class="text-sm text-gray-500">
                                @if($salle->planSalle)
                                    {{ count($salle->planSalle) }} rangées configurées
                                @else
                                    Non configuré
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if(!empty($salle->qualiteProjection))
                                    {{ implode(', ', $salle->qualiteProjection) }}
                                @else
                                    Standard
                                @endif
                            </div>
                            <div class="text-sm text-gray-500">
                                @if($salle->climatisation ?? false)<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 ml-1">Clim</span>@endif
                                @if($salle->accessibilitePmr ?? false)<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 ml-1">PMR</span>@endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusConfig = match($salle->statut) {
                                    'ACTIVE' => ['label' => 'Active', 'color' => 'green'],
                                    'MAINTENANCE' => ['label' => 'Maintenance', 'color' => 'yellow'],
                                    'RENOVATION' => ['label' => 'Rénovation', 'color' => 'orange'],
                                    'HORS_SERVICE' => ['label' => 'Hors service', 'color' => 'red'],
                                    default => ['label' => 'Inconnu', 'color' => 'gray'],
                                };
                            @endphp
                            <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-{{ $statusConfig['color'] }}-100 text-{{ $statusConfig['color'] }}-800">
                                <span class="size-1.5 inline-block rounded-full bg-{{ $statusConfig['color'] }}-800"></span>
                                {{ $statusConfig['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                            <div class="flex items-center justify-end gap-x-2">
                                <x-button
                                    :href="route('admin.salles.show', $salle->uuid)"
                                    size="sm"
                                    theme="admin"
                                    variant="outlined">
                                    Voir
                                </x-button>
                                <x-button
                                    :href="route('admin.salles.edit', $salle->uuid)"
                                    size="sm"
                                    theme="admin">
                                    Modifier
                                </x-button>
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
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune salle trouvée</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if(request('search'))
                    Aucune salle ne correspond à votre recherche "{{ request('search') }}".
                @else
                    Commencez par créer votre première salle.
                @endif
            </p>
        </div>
        @endif
    </div>
@endsection
