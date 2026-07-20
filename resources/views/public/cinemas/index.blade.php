@extends('layouts.cinema')

@section('title', 'Nos Cinémas')

@section('content')
    <!-- Header -->
    <div class="bg-black text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gold mb-4 flex justify-center items-end"><x-eos-chair class="w-12 h-12 mr-2 inline-block"/>Nos Cinémas</h1>
                <p class="text-xl text-gray-300">
                    Découvrez toutes les séances disponibles dans nos cinémas
                </p>

                <p class="mt-2 text-gray-400">
                    Découvrez nos établissements Cinéphoria à travers la France.
                    Chaque cinéma offre une expérience unique avec les dernières technologies.
                </p>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-gray-900 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <form method="GET" class="flex flex-wrap gap-4 items-center">
                <div class="flex-1 min-w-64">
                    <input type="text" name="location" value="{{ $filters['location'] ?? '' }}"
                        placeholder="Rechercher par ville..."
                        class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded text-white placeholder-gray-400 focus:outline-none focus:border-gold">
                </div>

                <x-button type="submit" color="primary" variant="solid" theme="cinema" size="md">
                    Rechercher
                </x-button>

                @if (!empty($filters['location']))
                    <a href="{{ route('cinemas.index') }}" class="text-gray-400 hover:text-gold transition-colors">
                        Effacer les filtres
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Liste des cinémas -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            @forelse($cinemas as $cinema)
                <div
                    class="bg-gray-900 border border-gray-800 rounded-lg overflow-hidden hover:border-gold transition-colors flex flex-col h-full">
                    <!-- Header -->
                    <div class="p-6 border-b border-gray-800">
                        <x-heading level="3" color="cinema-gold" class="mb-2">
                            {{ $cinema->nom }}
                        </x-heading>
                        <div class="text-gray-300 space-y-1">
                            <p class="flex items-center">
                                <svg class="w-4 h-4 mr-2 " fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $cinema->adresse }}
                            </p>
                            <p class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8h1m-1-4h1m4 4h1m-1-4h1" />
                                </svg>
                                {{ $cinema->ville }} {{ $cinema->code_postal }}
                            </p>
                            <p class="flex items-center">
                                <svg class="w-4 h-4 mr-2 " fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $cinema->telephone }}
                            </p>
                        </div>
                    </div>

                    <!-- Informations -->
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="mb-4">
                            <div>
                                <span class="text-sm text-gray-400">Salles</span>
                                <x-text size="lg" weight="semibold"
                                    color="cinema-gold">{{ $cinema->nombre_salles }}</x-text>
                            </div>
                        </div>

                        <!-- Services -->
                        @if (!empty($cinema->services))
                            <div class="mb-4">
                                <span class="text-sm text-gray-400 block mb-2">Services</span>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($cinema->services as $service)
                                        <span class="py-1">
                                            {{ $service }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Horaires -->
                        @if (!empty($cinema->horaires_ouverture))
                            <div class="mb-6">
                                <span class="text-sm text-gray-400 block mb-2">Horaires aujourd'hui</span>
                                @php
                                    $today = strtolower(now()->locale('fr')->dayName);
                                    $joursFr = [
                                        'monday' => 'lundi',
                                        'tuesday' => 'mardi',
                                        'wednesday' => 'mercredi',
                                        'thursday' => 'jeudi',
                                        'friday' => 'vendredi',
                                        'saturday' => 'samedi',
                                        'sunday' => 'dimanche',
                                    ];
                                    $todayFr = $joursFr[now()->format('l')] ?? 'lundi';
                                @endphp
                                <p class="text-white">
                                    {{ $cinema->horaires_ouverture[$todayFr] ?? 'Horaires non disponibles' }}
                                </p>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="mt-auto pt-4">
                            <x-button href="{{ route('cinemas.show', $cinema->cinema_id) }}" color="primary"
                                variant="solid" theme="cinema" size="sm" class="w-full">
                                Voir détails
                            </x-button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-6xl text-gray-600 mb-4">🎬</div>
                    <h3 class="text-xl text-gray-400 mb-2">Aucun cinéma trouvé</h3>
                    <p class="text-gray-500">Essayez de modifier vos critères de recherche.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($cinemas->hasPages())
            <div class="mt-12">
                {{ $cinemas->withQueryString()->links() }}
            </div>
        @endif
    </div>



    <style>
        /* Pagination custom styling pour le thème cinéma */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }

        .pagination a,
        .pagination span {
            padding: 0.5rem 1rem;
            background-color: #1f2937;
            color: #d1d5db;
            border: 1px solid #374151;
            border-radius: 0.375rem;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
        }

        .pagination a:hover {
            background-color: #d4af37;
            color: #000;
            border-color: #d4af37;
        }

        .pagination .active span {
            background-color: #d4af37;
            color: #000;
            border-color: #d4af37;
        }
    </style>
@endsection

@push('styles')
<style>
:root {
    --gold: #d4af37;
}

.text-gold {
    color: var(--gold);
}

.bg-gold {
    background-color: var(--gold);
}

.border-gold {
    border-color: var(--gold);
}

.bg-gold\/90 {
    background-color: rgba(212, 175, 55, 0.9);
}

.hover\:bg-gold:hover {
    background-color: var(--gold);
}

.hover\:bg-gold\/90:hover {
    background-color: rgba(212, 175, 55, 0.9);
}

.hover\:bg-gold\/80:hover {
    background-color: rgba(212, 175, 55, 0.8);
}

.hover\:text-gold:hover {
    color: var(--gold);
}

.hover\:text-gold\/80:hover {
    color: rgba(212, 175, 55, 0.8);
}

.hover\:border-gold:hover {
    border-color: var(--gold);
}

.focus\:ring-gold:focus {
    --tw-ring-color: var(--gold);
}

.focus\:border-gold:focus {
    border-color: var(--gold);
}
</style>
@endpush
