@extends('layouts.admin')

@section('title', 'Gestion des Clients')

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <a href="#" class="hover:text-gray-900">Utilisateurs</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Clients</span>
    </nav>
@endsection

@section('subtitle', 'Gérez les comptes clients de votre plateforme')

@section('actions')
    <button disabled
       class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-gray-400 text-white cursor-not-allowed disabled:opacity-50 disabled:pointer-events-none">
        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14"/>
            <path d="M12 5v14"/>
        </svg>
        Nouveau Client (Bientôt)
    </button>
@endsection

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
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
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
                        <th scope="col" class="px-6 py-3 text-end text-xs font-medium text-gray-500 uppercase tracking-wide">Actions</th>
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
                                            {{ strtoupper(substr($client->profil?->prenom ?? 'U', 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $client->profil?->full_name ?? 'Non renseigné' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        ID: {{ $client->id }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $client->credential?->email ?? 'Non renseigné' }}</div>
                            @if($client->profil?->telephone)
                            <div class="text-sm text-gray-500">{{ $client->profil->telephone }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($client->is_active)
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
                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                            <div class="hs-dropdown relative inline-block">
                                <button type="button" class="hs-dropdown-toggle py-1.5 px-2 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none">
                                    Actions
                                    <svg class="hs-dropdown-open:rotate-180 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m6 9 6 6 6-6"/>
                                    </svg>
                                </button>
                                <div class="hs-dropdown-menu w-48 transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden z-10 bg-white shadow-md rounded-lg border border-gray-200 p-1 space-y-0.5">
                                    <button class="w-full flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-400 cursor-not-allowed" disabled>
                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        Voir (Bientôt)
                                    </button>
                                    <button class="w-full flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-400 cursor-not-allowed" disabled>
                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 20h9"/>
                                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
                                        </svg>
                                        Modifier (Bientôt)
                                    </button>
                                    <div class="border-t border-gray-200 my-1"></div>
                                    <button type="button" class="w-full flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-red-600 hover:bg-red-50 focus:bg-red-50"
                                            onclick="confirmDelete('{{ $client->id }}', '{{ $client->profil?->full_name ?? $client->email }}')">
                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18"/>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                        </svg>
                                        Supprimer
                                    </button>
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
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun client trouvé</h3>
            <p class="mt-1 text-sm text-gray-500">
                @if($search)
                    Aucun client ne correspond à votre recherche "{{ $search }}".
                @else
                    Commencez par créer votre premier client.
                @endif
            </p>
            <div class="mt-6">
                @if($search)
                <a href="{{ route('admin.users.clients.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Voir tous les clients
                </a>
                @else
                <button disabled
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-400 cursor-not-allowed opacity-50">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" />
                    </svg>
                    Nouveau Client (Bientôt)
                </button>
                @endif
            </div>
        </div>
        @endif
    </div>

<script>
function confirmDelete(clientId, clientName) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer le client "${clientName}" ?\n\nCette action est irréversible.`)) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/clients/${clientId}`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
