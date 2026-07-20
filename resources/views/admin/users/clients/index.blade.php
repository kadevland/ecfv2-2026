@extends('layouts.admin')

@section('title', 'Gestion des Clients')

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        {{-- <a href="#" class="hover:text-gray-900">Utilisateurs</a>
        <span>/</span> --}}
        <span class="text-gray-900 font-medium">Clients</span>
    </nav>
@endsection

@section('subtitle', 'Gérez les comptes clients de votre plateforme')

@section('content')
    <!-- Search and Filters -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 sm:p-6 mb-6">
        <form method="GET" action="{{ route('admin.users.clients.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="search"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Rechercher par nom, prénom ou email..."
                       class="py-2 px-3 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none">
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:bg-blue-700">
                    Rechercher
                </button>
                @if($search)
                <a href="{{ route('admin.users.clients.index') }}"
                   class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 text-gray-500 hover:border-gray-300 hover:text-gray-600 focus:outline-none focus:border-gray-300 focus:text-gray-600">
                    Effacer
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Clients Table -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">
                Liste des Clients
                <span class="text-sm font-normal text-gray-500">({{ $clients->total() }} total)</span>
            </h2>
        </div>

        @if($clients->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Client</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Contact</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Statut</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Inscription</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($clients as $client)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-blue-600 font-medium text-sm">
                                            {{ strtoupper(substr($client->prenom ?? 'U', 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $client->full_name ?? 'Non renseigné' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        ID: {{ $client->user_uuid }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $client->email ?? 'Non renseigné' }}</div>
                            @if($client->telephone)
                            <div class="text-sm text-gray-500">{{ $client->telephone }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($client->user?->is_active ?? true)
                            <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <span class="size-1.5 inline-block rounded-full bg-green-800"></span>
                                Actif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <span class="size-1.5 inline-block rounded-full bg-red-800"></span>
                                Inactif
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $client->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="relative inline-block text-left" x-data="{ open: false }">
                                <button @click="open = !open" type="button" class="inline-flex justify-center items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" id="menu-button-{{ $loop->index }}" aria-expanded="true" aria-haspopup="true">
                                    Actions
                                    <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <div class="py-1">
                                        <a href="{{ route('admin.users.clients.show', $client->user_uuid) }}" class="flex px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Voir les détails
                                        </a>
                                        <a href="{{ route('admin.users.clients.edit', $client->user_uuid) }}" class="flex px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                            Modifier
                                        </a>
                                        @if($client->user?->is_active ?? true)
                                        <button type="button" class="flex w-full px-4 py-2 text-sm text-orange-700 hover:bg-orange-50" onclick="if(confirm('Êtes-vous sûr de vouloir désactiver ce client ?')) { /* TODO: Deactivate action */ }">
                                            <svg class="mr-3 h-5 w-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                            </svg>
                                            Désactiver
                                        </button>
                                        @else
                                        <button type="button" class="flex w-full px-4 py-2 text-sm text-green-700 hover:bg-green-50" onclick="if(confirm('Êtes-vous sûr de vouloir réactiver ce client ?')) { /* TODO: Activate action */ }">
                                            <svg class="mr-3 h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Réactiver
                                        </button>
                                        @endif
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
        @if($clients->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $clients->appends(request()->query())->links() }}
        </div>
        @endif

        @else
        <div class="text-center py-12">
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun client trouvé</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if($search)
                    Aucun client ne correspond à votre recherche "{{ $search }}".
                @else
                    Commencez par créer votre premier client.
                @endif
            </p>
        </div>
        @endif
    </div>
@endsection
