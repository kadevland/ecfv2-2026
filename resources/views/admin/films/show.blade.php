@extends('layouts.admin')

@section('title', $film->titre)

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.films.index') }}" class="hover:text-gray-900">Films</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">{{ $film->titre }}</span>
    </nav>
@endsection

@section('actions')
    <div class="flex space-x-3">
        <x-button
            href="{{ route('admin.seances.index', ['film_id' => $film->uuid]) }}"
            theme="admin"
            variant="outlined"
            icon="heroicon-o-calendar-days"
            size="md"
        >
            Gérer les séances
        </x-button>
        <x-button
            href="{{ route('admin.seances.create', ['film_id' => $film->uuid]) }}"
            theme="admin"
            icon="heroicon-o-plus"
            size="md"
        >
            Nouvelle séance
        </x-button>
        <x-button
            href="{{ route('admin.films.edit', $film->uuid) }}"
            color="primary"
            theme="admin"
            icon="heroicon-o-pencil"
            size="md"
        >
            Modifier
        </x-button>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Affiche --}}
        <div class="lg:col-span-1">
            <x-card theme="admin" variant="shadow" class="bg-white">
                @if($film->afficheUrl)
                    <img
                        src="{{ $film->afficheUrl }}"
                        alt="Affiche {{ $film->titre }}"
                        class="w-full h-auto rounded-lg"
                    >
                @else
                    <div class="aspect-[2/3] bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                @endif
            </x-card>
        </div>

        {{-- Informations principales --}}
        <div class="lg:col-span-2 space-y-6">
            <x-card theme="admin" variant="shadow" class="bg-white">
                <x-slot:header>
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $film->titre }}</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $film->estActif ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $film->estActif ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                    @if($film->titreFr && $film->titreFr !== $film->titre)
                        <p class="text-gray-600 mt-1">{{ $film->titreFr }}</p>
                    @endif
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Réalisateur(s)</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ implode(', ', $film->realisateurs) }}</dd>
                    </div>

                    @if(!empty($film->acteursPrincipaux))
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Acteurs principaux</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ implode(', ', $film->acteursPrincipaux) }}</dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Genres</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ implode(', ', $film->genres) }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Durée</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $film->dureeFormatted }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Classification</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $film->classification }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date de sortie</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($film->dateSortie)->format('d/m/Y') }}</dd>
                    </div>

                    @if($film->dateFinExploitation)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fin d'exploitation</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($film->dateFinExploitation)->format('d/m/Y') }}</dd>
                        </div>
                    @endif

                    @if($film->langueOriginale)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Langue originale</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $film->langueOriginale }}</dd>
                        </div>
                    @endif
                </div>

                @if($film->resume)
                    <div class="mt-6">
                        <dt class="text-sm font-medium text-gray-500">Résumé</dt>
                        <dd class="mt-1 text-sm text-gray-900 leading-relaxed">{{ $film->resume }}</dd>
                    </div>
                @endif
            </x-card>

            {{-- Notes et liens --}}
            <x-card theme="admin" variant="shadow" class="bg-white">
                <x-slot:header>
                    <h3 class="text-lg font-semibold text-gray-900">Évaluations et médias</h3>
                </x-slot:header>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @if($film->notePresse)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Note presse</dt>
                            <dd class="mt-1 text-2xl font-bold text-blue-600">{{ $film->notePresse }}/10</dd>
                        </div>
                    @endif

                    @if($film->notePublic)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Note public</dt>
                            <dd class="mt-1 text-2xl font-bold text-green-600">{{ $film->notePublic }}/10</dd>
                        </div>
                    @endif

                    @if($film->noteMoyenneAvis)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Moyenne des avis ({{ $film->nombreAvis }})</dt>
                            <dd class="mt-1 text-2xl font-bold text-purple-600">{{ $film->noteMoyenneAvis }}/10</dd>
                        </div>
                    @endif
                </div>

                @if($film->bandeAnnonceUrl)
                    <div class="mt-6">
                        <x-button
                            href="{{ $film->bandeAnnonceUrl }}"
                            target="_blank"
                            color="secondary"
                            theme="admin"
                            icon="heroicon-o-play"
                        >
                            Voir la bande-annonce
                        </x-button>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
@endsection
