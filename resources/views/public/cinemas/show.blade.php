@extends('layouts.cinema')

@section('title', $cinema->nom)

@section('content')
<div class="bg-black text-white min-h-screen">
    <!-- Breadcrumb -->
    <div class="bg-gray-900 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="text-sm">
                <a href="{{ route('cinemas.index') }}" class="text-gray-400 hover:text-gold transition-colors">
                    Nos Cinémas
                </a>
                <span class="text-gray-600 mx-2">/</span>
                <span class="text-gold">{{ $cinema->nom }}</span>
            </nav>
        </div>
    </div>

    <!-- Header du cinéma -->
    <div class="bg-gradient-to-b from-gray-900 to-black py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <x-heading level="1" color="cinema-gold" size="5xl" class="mb-4">
                    {{ $cinema->nom }}
                </x-heading>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto mb-8">
                    {{ $cinema->description }}
                </p>

            </div>
        </div>
    </div>

    <!-- Retour à la liste -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="text-center">
            <x-button href="{{ route('cinemas.index') }}"
                      color="secondary"
                      variant="outlined"
                      theme="cinema"
                      size="md"
                      icon="heroicon-m-arrow-left"
                      icon-position="left">
                Retour à la liste des cinémas
            </x-button>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Informations principales -->
            <div class="space-y-6">
                <!-- Coordonnées -->
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-gold mb-6">Informations pratiques</h2>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-4">Adresse</h3>
                            <div class="space-y-2 text-gray-300">
                                <p class="flex items-start">
                                    <svg class="w-5 h-5 mr-3 mt-0.5 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $cinema->adresse }}<br>
                                    {{ $cinema->code_postal }} {{ $cinema->ville }}
                                </p>
                                <p class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <a href="tel:{{ $cinema->telephone }}" class="hover:text-gold transition-colors">
                                        {{ $cinema->telephone }}
                                    </a>
                                </p>
                                <p class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <a href="mailto:{{ $cinema->email }}" class="hover:text-gold transition-colors">
                                        {{ $cinema->email }}
                                    </a>
                                </p>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-white mb-4">Capacité</h3>
                            <div class="bg-gray-800 rounded-lg p-4">
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-gold mb-1">{{ $cinema->nombre_salles }}</div>
                                    <div class="text-sm text-gray-400">
                                        {{ $cinema->nombre_salles > 1 ? 'Salles' : 'Salle' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Salles actives -->
                @if(!empty($cinema->salles))
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-gold mb-6">Nos salles</h2>

                    <div class="grid sm:grid-cols-2 gap-4">
                        @foreach($cinema->salles as $salle)
                            <div class="bg-gray-800 border border-gray-700 rounded-lg p-4 hover:border-gold/50 transition-colors">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <h3 class="font-semibold text-white">
                                            {{ $salle->nom ?: 'Salle ' . ($loop->iteration) }}
                                        </h3>
                                        <div class="space-y-1">
                                            <p class="text-sm text-gray-400">
                                                {{ $salle->capaciteTotale ?: 0 }} places au total
                                            </p>
                                            @if(isset($salle->capacitePmr) && $salle->capacitePmr > 0)
                                            <p class="text-xs text-blue-400">
                                                {{ $salle->capacitePmr }} places PMR
                                            </p>
                                            @endif
                                        </div>
                                    </div>
                                    @if($salle->accessibilitePmr)
                                    <div class="bg-blue-600/20 px-2 py-1 rounded text-xs text-blue-400 font-medium">
                                        PMR
                                    </div>
                                    @endif
                                </div>

                                <!-- Qualités avec labels des Enums -->
                                <x-salle-qualites
                                    :qualite-projection="$salle->qualiteProjection ?? []"
                                    :qualite-sonore="$salle->qualiteSonore ?? []"
                                />

                                <!-- Équipements -->
                                <div class="flex items-center gap-4 text-xs text-gray-400">
                                    @if($salle->climatisation)
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                                        </svg>
                                        <span>Climatisé</span>
                                    </div>
                                    @endif

                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        <span>{{ $salle->typeEcran ?? 'Standard' }}</span>
                                    </div>

                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M9 12a3 3 0 106 0 3 3 0 00-6 0z"/>
                                        </svg>
                                        <span>{{ $salle->qualiteSon ?? 'Standard' }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Localisation -->
                @if($cinema->latitude && $cinema->longitude)
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-gold mb-6">Localisation</h2>

                    <div class="space-y-4">
                        <!-- Adresse -->
                        <div class="text-gray-300">
                            <p>{{ $cinema->adresse }}</p>
                            <p>{{ $cinema->codePostal }} {{ $cinema->ville }}</p>
                        </div>

                        <!-- Carte interactive -->
                        <div class="rounded-lg overflow-hidden border border-gray-700">
                            <x-map
                                :latitude="$cinema->latitude"
                                :longitude="$cinema->longitude"
                                :title="$cinema->nom"
                                height="350px"
                                class="w-full"
                            />
                        </div>
                    </div>
                </div>
                @endif

                <!-- Services -->
                @if(!empty($cinema->services))
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-gold mb-6">Services et équipements</h2>

                    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($cinema->services as $service)
                            <div class="flex items-center gap-3 bg-gray-800 p-4 rounded-lg">
                                @switch($service)
                                    @case('IMAX')
                                        <div class="w-10 h-10 bg-gold/20 rounded-lg flex items-center justify-center">
                                            <span class="text-gold font-bold text-sm">IMAX</span>
                                        </div>
                                        @break
                                    @case('4K')
                                        <div class="w-10 h-10 bg-gold/20 rounded-lg flex items-center justify-center">
                                            <span class="text-gold font-bold text-xs">4K</span>
                                        </div>
                                        @break
                                    @case('Dolby Atmos')
                                        <div class="w-10 h-10 bg-gold/20 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M9 12a3 3 0 106 0 3 3 0 00-6 0z"/>
                                            </svg>
                                        </div>
                                        @break
                                    @default
                                        <div class="w-10 h-10 bg-gold/20 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                @endswitch

                                <div>
                                    <div class="font-medium text-white">{{ $service }}</div>
                                    <div class="text-sm text-gray-400">Disponible</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Accès transport -->
                @if(!empty($cinema->acces))
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-gold mb-6">Comment s'y rendre</h2>

                    <div class="space-y-4">
                        @foreach($cinema->acces as $type => $info)
                            <div class="flex items-start gap-4">
                                @switch($type)
                                    @case('Metro')
                                        <div class="w-10 h-10 bg-blue-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <span class="text-blue-400 font-bold text-xs">M</span>
                                        </div>
                                        @break
                                    @case('Tram')
                                        <div class="w-10 h-10 bg-green-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <span class="text-green-400 font-bold text-xs">T</span>
                                        </div>
                                        @break
                                    @case('Bus')
                                        <div class="w-10 h-10 bg-orange-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2 2H8a2 2 0 01-2-2v0a2 2 0 01-2-2V9a2 2 0 012-2h2.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707z"/>
                                            </svg>
                                        </div>
                                        @break
                                    @default
                                        <div class="w-10 h-10 bg-gray-600/20 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            </svg>
                                        </div>
                                @endswitch

                                <div>
                                    <div class="font-medium text-white">{{ $type }}</div>
                                    <div class="text-gray-300">{{ $info }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Informations pratiques -->
            <div class="space-y-6">
                <!-- Horaires -->
                @if(!empty($cinema->horaires_ouverture))
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gold mb-4">Horaires d'ouverture</h2>

                    <div class="space-y-3">
                        @foreach($cinema->horaires_ouverture as $jour => $horaires)
                            @php
                                $isToday = strtolower(now()->locale('fr')->dayName) === $jour ||
                                          (now()->format('l') === 'Monday' && $jour === 'lundi') ||
                                          (now()->format('l') === 'Tuesday' && $jour === 'mardi') ||
                                          (now()->format('l') === 'Wednesday' && $jour === 'mercredi') ||
                                          (now()->format('l') === 'Thursday' && $jour === 'jeudi') ||
                                          (now()->format('l') === 'Friday' && $jour === 'vendredi') ||
                                          (now()->format('l') === 'Saturday' && $jour === 'samedi') ||
                                          (now()->format('l') === 'Sunday' && $jour === 'dimanche');
                            @endphp

                            <div class="flex justify-between items-center {{ $isToday ? 'bg-gold/10 border border-gold/30 rounded px-3 py-2' : '' }}">
                                <span class="font-medium {{ $isToday ? 'text-gold' : 'text-gray-300' }}">
                                    {{ ucfirst($jour) }}
                                </span>
                                <span class="{{ $isToday ? 'text-gold font-medium' : 'text-gray-400' }}">
                                    @if(is_array($horaires))
                                        {{ $horaires['debut_matin'] ?? '09:00' }}-{{ $horaires['fin_matin'] ?? '12:30' }} / {{ $horaires['debut_apres'] ?? '15:00' }}-{{ $horaires['fin_apres'] ?? '22:30' }}
                                    @else
                                        {{ $horaires }}
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Actions rapides -->
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gold mb-4">Actions rapides</h2>

                    <div class="space-y-3">
                        <x-button type="button"
                                  data-hs-overlay="#search-modal"
                                  color="primary"
                                  variant="solid"
                                  theme="cinema"
                                  size="lg"
                                  class="w-full"
                                  icon="heroicon-m-magnifying-glass">
                            Rechercher un film
                        </x-button>

                        <a href="tel:{{ $cinema->telephone }}"
                           class="w-full bg-gray-800 text-white text-center py-3 px-4 rounded font-medium hover:bg-gray-700 transition-colors block">
                            Appeler le cinéma
                        </a>

                        <a href="mailto:{{ $cinema->email }}"
                           class="w-full border border-gray-700 text-gray-300 text-center py-3 px-4 rounded font-medium hover:border-gold hover:text-gold transition-colors block">
                            Envoyer un email
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- Carte pleine largeur en bas -->
        <div class="mt-12">
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-8">
                <div class="text-center">
                    <x-heading level="2" color="cinema-gold" class="mb-4">
                        Découvrez nos films
                    </x-heading>
                    <p class="text-gray-400 mb-6 max-w-2xl mx-auto">
                        Explorez notre programmation et réservez vos séances dans ce cinéma.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <x-button type="button"
                                  data-hs-overlay="#search-modal"
                                  color="primary"
                                  variant="solid"
                                  theme="cinema"
                                  size="lg"
                                  icon="heroicon-m-magnifying-glass"
                                  icon-position="left">
                            Rechercher un film
                        </x-button>
                        <x-button href="{{ route('films.index') }}"
                                  color="secondary"
                                  variant="outlined"
                                  theme="cinema"
                                  size="lg">
                            Voir tous les films
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection