@extends('layouts.admin')

@section('title', $cinema->nom)

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.cinemas.index') }}" class="hover:text-gray-900">Cinémas</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">{{ $cinema->nom }}</span>
    </nav>
@endsection

@section('actions')
    <div class="flex space-x-3">
        <x-button :href="route('admin.cinemas.edit', $cinema->uuid)" color="primary" theme="admin" icon="heroicon-o-pencil">
            Modifier
        </x-button>
        {{-- <x-button href="#" color="{{ $cinema->estActif ? 'warning' : 'success' }}" variant="outlined" theme="admin"
            icon="{{ $cinema->estActif ? 'heroicon-o-x-mark' : 'heroicon-o-check' }}" onclick="confirmToggleStatus()">
            {{ $cinema->estActif ? 'Fermer' : 'Rouvrir' }}
        </x-button> --}}
    </div>
@endsection

@section('content')
    <div class="flex flex-col gap-6">
        {{-- Cinema Information --}}
        <x-card theme="admin" variant="shadow" title="{{ $cinema->nom }}" :subtitle="$cinema->adresse . ', ' . $cinema->codePostal . ' ' . $cinema->ville" :hasHeader="true">
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Informations générales</h2>
                    <x-badge :color="$cinema->estActif ? 'green' : 'red'">
                        {{ $cinema->estActif ? 'Ouvert' : 'Fermé' }}
                    </x-badge>
                </div>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    @if ($cinema->description)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-gray-900">{{ $cinema->description }}</dd>
                        </div>
                    @endif

                    <div>
                        <dt class="text-sm font-medium text-gray-500">Coordonnées GPS</dt>
                        <dd class="mt-1 text-gray-900">
                            Lat: {{ $cinema->latitude }}, Lng: {{ $cinema->longitude }}
                        </dd>
                    </div>
                </div>

                <div class="space-y-4">
                    @if ($cinema->telephone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                            <dd class="mt-1">
                                <a href="tel:{{ $cinema->telephone }}" class="text-blue-600 hover:text-blue-500">
                                    {{ $cinema->telephone }}
                                </a>
                            </dd>
                        </div>
                    @endif

                    @if ($cinema->email)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1">
                                <a href="mailto:{{ $cinema->email }}" class="text-blue-600 hover:text-blue-500">
                                    {{ $cinema->email }}
                                </a>
                            </dd>
                        </div>
                    @endif

                    @if ($cinema->siteWeb)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Site Web</dt>
                            <dd class="mt-1">
                                <a href="{{ $cinema->siteWeb }}" target="_blank" class="text-blue-600 hover:text-blue-500">
                                    {{ $cinema->siteWeb }}
                                </a>
                            </dd>
                        </div>
                    @endif
                </div>
            </div>
        </x-card>

        {{-- Cinema Stats --}}
        <x-card theme="admin" variant="shadow" title="Statistiques" subtitle="Données d'activité du cinéma" size="md">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $cinema->nombreSalles }}</div>
                    <div class="text-sm text-gray-500">Salles</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $cinema->horairesOuverture ? count($cinema->horairesOuverture->getJoursOuvertsSafe()) : 0 }}</div>
                    <div class="text-sm text-gray-500">Jours ouverts</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600">0</div>
                    <div class="text-sm text-gray-500">Séances ce mois</div>
                </div>
            </div>
        </x-card>

        {{-- Horaires d'ouverture --}}
        @if ($cinema->horairesOuverture)
            <x-card theme="admin" variant="shadow" title="Horaires d'ouverture" subtitle="Horaires de fonctionnement du cinéma">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-4">
                    @foreach (\App\Domain\Shared\Enums\JourSemaine::cases() as $jour)
                        @php
                            $horaire = $cinema->horairesOuverture->getHoraireJour($jour);
                        @endphp
                        <div class="text-center">
                            <div class="font-medium text-gray-900 mb-2">{{ $jour->getLabel() }}</div>
                            @if ($horaire->estFerme())
                                <div class="text-red-600 text-sm">Fermé</div>
                            @else
                                <div class="space-y-1 text-sm">
                                    @if ($horaire->debutMatin && $horaire->finMatin)
                                        <div class="text-gray-900">
                                            {{ $horaire->debutMatin }} - {{ $horaire->finMatin }}
                                        </div>
                                    @endif
                                    @if (!$horaire->journeeComplete() && $horaire->debutApres && $horaire->finApres)
                                        <div class="text-gray-900">
                                            {{ $horaire->debutApres }} - {{ $horaire->finApres }}
                                        </div>
                                    @endif
                                    @if ($horaire->dureeMaxSeanceMatin)
                                        <div class="text-xs text-gray-500">
                                            Séances: +{{ floor($horaire->dureeMaxSeanceMatin / 60) }}h{{ str_pad($horaire->dureeMaxSeanceMatin % 60, 2, '0', STR_PAD_LEFT) }}
                                            @if ($horaire->dureeMaxSeanceApres)
                                                / +{{ floor($horaire->dureeMaxSeanceApres / 60) }}h{{ str_pad($horaire->dureeMaxSeanceApres % 60, 2, '0', STR_PAD_LEFT) }}
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-card>
        @endif

        {{-- Map Preview --}}
        @if ($cinema->latitude && $cinema->longitude)
            <x-card theme="admin" variant="shadow" title="Localisation" :subtitle="$cinema->nom . ' - ' . $cinema->ville">
                <x-map
                    :latitude="$cinema->latitude"
                    :longitude="$cinema->longitude"
                    :title="$cinema->nom"
                    height="300px"
                />
            </x-card>
        @endif
    </div>

    {{-- Toggle Status Confirmation --}}
    <script>
        function confirmToggleStatus() {
            const isActive = {{ $cinema->estActif ? 'true' : 'false' }};
            const action = isActive ? 'fermer' : 'rouvrir';
            const message = isActive ?
                'Êtes-vous sûr de vouloir fermer ce cinéma ? Il ne sera plus visible pour les clients.' :
                'Êtes-vous sûr de vouloir rouvrir ce cinéma ? Il redeviendra visible pour les clients.';

            if (confirm(message)) {
                // Créer un formulaire pour envoyer la requête POST
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.cinemas.toggle-status', $cinema->uuid) }}';

                // Ajouter le token CSRF
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                // Ajouter le formulaire au DOM et le soumettre
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endsection
