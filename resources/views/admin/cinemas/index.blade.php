@extends('layouts.admin')

@section('title', 'Gestion des Cinémas')

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Cinémas</span>
    </nav>
@endsection

@section('actions')
    <x-button href="{{ route('admin.cinemas.create') }}" color="primary" theme="admin" icon="heroicon-o-plus" size="md">
        Nouveau Cinéma
    </x-button>
@endsection

@section('content')
    {{-- Search & Filters --}}
    <x-card theme="admin" variant="shadow" class="mb-6 bg-white">
        <form method="GET" action="{{ route('admin.cinemas.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                    <input type="text" name="location" value="{{ data_get($filters, 'location') }}"
                        placeholder="Ville, adresse..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pays</label>
                    <select name="pays"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les pays</option>
                        <option value="FR" @selected(data_get($filters, 'pays') == 'FR')>France</option>
                        <option value="BE" @selected(data_get($filters, 'pays') == 'BE')>Belgique</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <x-button type="submit" theme="admin" class="mr-2">
                        Rechercher
                    </x-button>
                    @if (count($filters))
                        <x-button href="{{ route('admin.cinemas.index') }}" variant="outlined" theme="admin">
                            Réinitialiser
                        </x-button>
                    @endif
                </div>
            </div>
        </form>
    </x-card>

    {{-- Cinemas Grid --}}
    @if (count($cinemas) > 0)
        <x-grid cols="1" gap="4" class="lg:grid-cols-2 xl:grid-cols-3">
            @foreach ($cinemas as $cinema)
                <x-card theme="admin" variant="shadow" :title="$cinema->nom" :subtitle="$cinema->adresse . ', ' . $cinema->codePostal . ' ' . $cinema->ville" :hasFooter="true">
                    <div class="space-y-1">
                        <p class="text-sm text-gray-700">
                            <span class="font-medium">Adresse:</span> {{ $cinema->adresse }}, {{ $cinema->ville }}
                            {{ $cinema->codePostal }}
                        </p>


                        @if ($cinema->telephone)
                            <p class="text-sm text-gray-700">
                                <span class="font-medium">Tél:</span> {{ $cinema->telephone }}
                            </p>
                        @endif
                        @if ($cinema->email)
                            <p class="text-sm text-gray-700">
                                <span class="font-medium">Email:</span> {{ $cinema->email }}
                            </p>
                        @endif

                        <p class="text-sm text-gray-700">
                            <span class="font-medium">Salle(s):</span> {{ $cinema->nombreSalles }}
                        </p>
                        <p class="text-sm text-gray-700">
                            <span class="font-medium">Accessibilitée:</span>
                            <x-badge theme="admin" :color="$cinema->accessibilitePmr ? 'success' : 'secondary'" :text="$cinema->accessibilitePmr ? 'PMR' : 'Standard'" size="sm">

                            </x-badge>
                        </p>

                    </div>
                    <x-slot name="footer">
                        <div class="flex items-center justify-between">

                            <div class="flex space-x-2">
                                <x-button :href="route('admin.cinemas.edit', $cinema->uuid)" size="sm" variant="outlined" theme="admin">
                                    Modifier
                                </x-button>
                                <x-button :href="route('admin.cinemas.show', $cinema->uuid)" size="sm" theme="admin">
                                    Voir
                                </x-button>
                            </div>
                        </div>
                    </x-slot>
                </x-card>

                {{-- <x-card
                    theme="admin"
                    variant="shadow"
                    :href="route('admin.cinemas.show', $cinema->uuid)"
                    :title="$cinema->nom"
                    :subtitle="$cinema->ville . ', ' . $cinema->codePostal"
                    size="sm"
                    :hasHeader="true"
                    :hasFooter="true"
                    :footerDivider="true"
                    class="bg-white hover:shadow-lg transition-shadow duration-200"
                >
                    <x-slot name="header">
                        <x-badge
                            :color="$cinema->accessibilitePmr ? 'green' : 'gray'"
                            size="sm"
                        >
                            {{ $cinema->accessibilitePmr ? 'PMR' : 'Standard' }}
                        </x-badge>
                    </x-slot>

                    <div class="space-y-1">
                        <p class="text-sm text-gray-700">
                            <span class="font-medium">Adresse:</span> {{ $cinema->adresse }}
                        </p>
                        @if ($cinema->telephone)
                            <p class="text-sm text-gray-700">
                                <span class="font-medium">Tél:</span> {{ $cinema->telephone }}
                            </p>
                        @endif
                        @if ($cinema->email)
                            <p class="text-sm text-gray-700">
                                <span class="font-medium">Email:</span> {{ $cinema->email }}
                            </p>
                        @endif
                    </div>

                    <x-slot name="footer">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">
                                {{ $cinema->nombreSalles }} salle(s)
                            </span>
                            <div class="flex space-x-2">
                                <x-button
                                    :href="route('admin.cinemas.edit', $cinema->uuid)"
                                    size="sm"
                                    variant="outlined"
                                    theme="admin"
                                >
                                    Modifier
                                </x-button>
                                <x-button
                                    :href="route('admin.cinemas.show', $cinema->uuid)"
                                    size="sm"
                                    theme="admin"
                                >
                                    Voir
                                </x-button>
                            </div>
                        </div>
                    </x-slot>
                </x-card> --}}
            @endforeach
        </x-grid>

        {{-- Pagination --}}

        {{ $cinemas->links() }}
    @else
        {{-- Empty State --}}
        <x-card theme="admin" variant="shadow" title="Aucun cinéma trouvé"
            subtitle="Commencez par ajouter votre premier cinéma" size="lg" class="text-center bg-white">
            <div class="text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m5 0v-6a1 1 0 011-1h0a1 1 0 011 1v6m-5 0v-6a1 1 0 011-1h0a1 1 0 011 1v6" />
                </svg>
                <x-button href="{{ route('admin.cinemas.create') }}" theme="admin" icon="heroicon-o-plus">
                    Créer un cinéma
                </x-button>
            </div>
        </x-card>
    @endif
@endsection
