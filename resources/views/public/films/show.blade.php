@extends('layouts.cinema')

@section('title', $film->titre)

@section('content')
<div class="bg-black text-white min-h-screen">
    <!-- Hero Section avec Backdrop -->
    <div class="relative">
        <!-- Backdrop Image -->
        <div class="relative h-96 lg:h-[500px] overflow-hidden">
            @if(isset($film->backdrop_url) && $film->backdrop_url)
                <img src="{{ $film->backdrop_url }}"
                     alt="{{ $film->titre }}"
                     class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900"></div>
            @endif
            <!-- Overlay gradient -->
            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/70 to-transparent"></div>
        </div>

        <!-- Hero Content -->
        <div class="absolute bottom-0 left-0 right-0 p-6 lg:p-12">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col lg:flex-row gap-8 items-end">
                    <!-- Affiche -->
                    <div class="w-48 lg:w-64 flex-shrink-0">
                        @if($film->affiche_url)
                            <img src="{{ $film->affiche_url }}"
                                 alt="{{ $film->titre }}"
                                 class="w-full rounded-lg shadow-2xl">
                        @else
                            <div class="w-full aspect-[2/3] bg-gray-800 rounded-lg flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Informations principales -->
                    <div class="flex-1">
                        <nav class="text-sm text-gray-300 mb-4">
                            <a href="{{ route('films.index') }}" class="hover:text-gold transition-colors">Films</a>
                            <span class="mx-2">›</span>
                            <span class="text-gold">{{ $film->titre }}</span>
                        </nav>

                        <x-heading level="1" color="cinema-gold" size="4xl" class="mb-4">
                            {{ $film->titre }}
                        </x-heading>

                        <div class="flex flex-wrap items-center gap-4 mb-6 text-gray-300">
                            <x-classification-badge :value="$film->classification" class="bg-black/50 px-3 py-1 rounded text-white" />
                            <span>{{ $film->duree }} min</span>
                            <span>{{ ucfirst($film->genre) }}</span>
                            @if($film->date_sortie)
                                <span>{{ $film->date_sortie->format('Y') }}</span>
                            @endif
                            @if($film->note_moyenne)
                                <div class="flex items-center gap-1">
                                    <span class="text-gold">⭐</span>
                                    <span class="text-gold font-semibold">{{ number_format($film->note_moyenne, 1) }}</span>
                                    @if($film->nombre_avis > 0 && false)
                                        <span class="text-sm text-gray-400">({{ $film->nombre_avis }} avis)</span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="flex gap-3">
                            <x-button href="{{ route('films.seances', $film->film_id) }}"
                                      color="primary"
                                      variant="solid"
                                      theme="cinema"
                                      size="lg"
                                      icon="heroicon-m-ticket">
                                Voir les séances
                            </x-button>
                            <x-button href="{{ route('films.index') }}"
                                      color="secondary"
                                      variant="outlined"
                                      theme="cinema"
                                      size="lg">
                                Retour aux films
                            </x-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid lg:grid-cols-2 gap-8 mb-8">
            <!-- Colonne principale 2/3 -->
            <div class="space-y-8">
                <!-- Synopsis -->
                @if($film->description)
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                    <x-heading level="2" color="cinema-gold" class="mb-4">
                        Synopsis
                    </x-heading>
                    <div class="text-gray-300 leading-relaxed">
                        {{ $film->description }}
                    </div>
                </div>
                @endif

                <!-- Séances du jour -->
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                    <x-heading level="2" color="cinema-gold" class="mb-4">
                        Séances d'aujourd'hui
                    </x-heading>
                    <div class="text-gray-400 mb-4">
                        {{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                    </div>

                    <!-- Séances d'aujourd'hui (données réelles) -->
                    @php
                        $today = now()->format('Y-m-d');
                        $seancesToday = $seances->get($today, collect());
                    @endphp

                    @if($seancesToday->isNotEmpty())
                        <div class="grid gap-4 md:grid-cols-2">
                            @foreach($seancesToday->take(4) as $seance)
                                <div class="border border-gray-700 rounded-lg p-4 hover:border-gold transition-colors">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xl font-bold text-white">{{ $seance->date_heure_debut->format('H:i') }}</span>
                                        <span class="bg-gray-800 text-gray-300 px-2 py-1 rounded text-sm">{{ $seance->version }}</span>
                                    </div>
                                    <div class="text-sm text-gray-400 mb-3">
                                        <div>{{ $seance->nom_cinema }}</div>
                                        <div>{{ $seance->nom_salle }} • {{ $seance->places_disponibles }} places disponibles</div>
                                    </div>
                                    <x-button href="{{ route('seance.reserver', $seance->seance_id) }}"
                                              color="primary"
                                              variant="solid"
                                              theme="cinema"
                                              size="sm"
                                              class="w-full">
                                        Réserver
                                    </x-button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-gray-400 text-center py-8">
                            Aucune séance prévue aujourd'hui pour ce film.
                        </div>
                    @endif

                    <div class="mt-4 text-center">
                        <x-button href="{{ route('films.seances', $film->film_id) }}"
                                  color="secondary"
                                  variant="outlined"
                                  theme="cinema"
                                  size="sm">
                            Voir toutes les séances
                        </x-button>
                    </div>
                </div>
            </div>

            <!-- Colonne latérale 1/3 -->
            <div class="space-y-8">
                <!-- Informations -->
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                    <x-heading level="2" color="cinema-gold" class="mb-4">
                        Informations
                    </x-heading>
                    <div class="space-y-3 text-sm">
                        @if($film->realisateur)
                            <div>
                                <span class="text-gray-400">Réalisateur :</span>
                                <span class="text-white ml-2">{{ $film->realisateur }}</span>
                            </div>
                        @endif
                        @if(isset($film->acteurs_principaux) && $film->acteurs_principaux)
                            <div>
                                <span class="text-gray-400">Acteurs principaux :</span>
                                <span class="text-white ml-2">{{ is_array($film->acteurs_principaux) ? implode(', ', $film->acteurs_principaux) : $film->acteurs_principaux }}</span>
                            </div>
                        @endif
                        <div>
                            <span class="text-gray-400">Genre :</span>
                            <span class="text-white ml-2">{{ ucfirst($film->genre) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400">Durée :</span>
                            <span class="text-white ml-2">{{ $film->duree }} minutes</span>
                        </div>
                        @if($film->date_sortie)
                            <div>
                                <span class="text-gray-400">Date de sortie :</span>
                                <span class="text-white ml-2">{{ $film->date_sortie->locale('fr')->isoFormat('D MMMM YYYY') }}</span>
                            </div>
                        @endif
                        <div>
                            <span class="text-gray-400">Classification :</span>
                            <span class="text-white ml-2">
                                @if($film->classification)
                                    {{ \App\Domain\Enums\ClassificationFilm::from($film->classification)->label() }}
                                @else
                                    Non classé
                                @endif
                            </span>
                        </div>
                        @if(isset($film->pays_origine) && $film->pays_origine)
                            <div>
                                <span class="text-gray-400">Pays d'origine :</span>
                                <span class="text-white ml-2">{{ $film->pays_origine }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Notes et Avis -->
                @if($film->note_moyenne || $film->nombre_avis > 0)
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                    <x-heading level="2" color="cinema-gold" class="mb-4">
                        Notes et Avis
                    </x-heading>
                    <div class="flex items-center gap-6 mb-4">
                        @if($film->note_moyenne)
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gold mb-1">{{ number_format($film->note_moyenne, 1) }}</div>
                                <div class="text-gold text-xl mb-1">⭐⭐⭐⭐⭐</div>
                                <div class="text-sm text-gray-400">Note moyenne</div>
                            </div>
                        @endif
                        @if($film->nombre_avis > 0)
                            <div class="text-center">
                                <div class="text-2xl font-bold text-white mb-1">{{ $film->nombre_avis }}</div>
                                <div class="text-sm text-gray-400">Avis spectateurs</div>
                            </div>
                        @endif
                    </div>
                    <x-button href="{{ route('films.ratings', $film->film_id) }}"
                              color="secondary"
                              variant="outlined"
                              theme="cinema"
                              size="sm">
                        Voir tous les avis
                    </x-button>
                </div>
                @endif

                <!-- Cinémas qui diffusent -->
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                    <x-heading level="2" color="cinema-gold" class="mb-4">
                        Cinémas qui diffusent
                    </x-heading>
                    <div class="space-y-3">
                        @if($cinemas && $cinemas->isNotEmpty())
                            @foreach($cinemas as $cinema)
                                <div class="flex items-center justify-between p-3 bg-gray-800 rounded-lg">
                                    <div>
                                        <div class="font-medium text-white">{{ $cinema['nom'] }}</div>
                                        <div class="text-sm text-gray-400">
                                            {{ $cinema['adresse'] }}
                                            @if($cinema['ville'])
                                                , {{ $cinema['ville'] }}
                                            @endif
                                        </div>
                                        @if($cinema['telephone'])
                                            <div class="text-xs text-gray-500 mt-1">{{ $cinema['telephone'] }}</div>
                                        @endif
                                    </div>
                                    <x-button href="{{ route('cinemas.show', $cinema['id']) }}"
                                              color="secondary"
                                              variant="outlined"
                                              theme="cinema"
                                              size="xs">
                                        Voir
                                    </x-button>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-gray-400 py-4">
                                Aucun cinéma ne diffuse actuellement ce film.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Planning des prochaines séances - Full width 3/3 -->
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
            <x-heading level="2" color="cinema-gold" class="mb-4">
                Planning des prochaines séances
            </x-heading>
            <div class="grid md:grid-cols-3 gap-6">
                @php
                    $nextDays = [
                        now()->addDay(),
                        now()->addDays(2),
                        now()->addDays(3)
                    ];
                @endphp

                @foreach($nextDays as $day)
                    @php
                        $dayKey = $day->format('Y-m-d');
                        $daySeances = $seances->get($dayKey, collect());
                    @endphp

                    <div>
                        <div class="text-sm text-gray-400 mb-2">{{ $day->locale('fr')->isoFormat('dddd D MMMM') }}</div>
                        @if($daySeances->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach($daySeances->take(3) as $seance)
                                    <span class="bg-gray-800 text-white px-2 py-1 rounded text-sm">{{ $seance->date_heure_debut->format('H:i') }}</span>
                                @endforeach
                                @if($daySeances->count() > 3)
                                    <span class="text-gray-400 px-2 py-1 text-sm">+{{ $daySeances->count() - 3 }} autres</span>
                                @endif
                            </div>
                        @else
                            <div class="text-gray-500 text-sm">Aucune séance</div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-6 text-center">
                <x-button href="{{ route('films.seances', $film->film_id) }}"
                          color="primary"
                          variant="solid"
                          theme="cinema"
                          size="md"
                          class="w-full md:w-auto">
                    Voir planning complet
                </x-button>
            </div>
        </div>
    </div>
</div>
@endsection
