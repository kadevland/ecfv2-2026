@extends('layouts.cinema')

@section('title', 'Mes Réservations')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('account') }}" class="text-gold hover:text-yellow-400 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-white">
                    Mes <span class="text-gold">Réservations</span>
                </h1>
                <p class="text-gray-300">Gérez vos réservations et téléchargez vos billets</p>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gray-900 rounded-lg p-4 border border-gray-800">
                <div class="text-2xl font-bold text-white">{{ $allCount }}</div>
                <div class="text-sm text-gray-400">Total réservations</div>
            </div>
            <div class="bg-gray-900 rounded-lg p-4 border border-gray-800">
                <div class="text-2xl font-bold text-green-400">{{ $activeCount }}</div>
                <div class="text-sm text-gray-400">Réservations actives</div>
            </div>
            <div class="bg-gray-900 rounded-lg p-4 border border-gray-800">
                <div class="text-2xl font-bold text-gray-400">{{ $pastCount }}</div>
                <div class="text-sm text-gray-400">Réservations passées</div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="mb-6">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('account.reservations') }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !$selectedStatus ? 'bg-cinema-gold text-black' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                Toutes ({{ $allCount }})
            </a>
            <a href="{{ route('account.reservations', ['statut' => 'EN_ATTENTE_PAIEMENT']) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $selectedStatus === 'EN_ATTENTE_PAIEMENT' ? 'bg-cinema-gold text-black' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                En attente
            </a>
            <a href="{{ route('account.reservations', ['statut' => 'CONFIRMEE']) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $selectedStatus === 'CONFIRMEE' ? 'bg-cinema-gold text-black' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                Confirmées
            </a>
            <a href="{{ route('account.reservations', ['statut' => 'PAYEE']) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $selectedStatus === 'PAYEE' ? 'bg-cinema-gold text-black' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                Payées
            </a>
            <a href="{{ route('account.reservations', ['statut' => 'UTILISEE']) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $selectedStatus === 'UTILISEE' ? 'bg-cinema-gold text-black' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
                Utilisées
            </a>
        </div>
    </div>

    <!-- Liste des réservations -->
    <div class="space-y-4">
        @forelse($reservations as $reservation)
            <div class="bg-gray-900 rounded-xl border border-gray-800">
                <x-reservation-card :reservation="$reservation" />
            </div>
        @empty
        <div class="p-12 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-800 mb-4">
                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a1 1 0 001 1h1a1 1 0 001-1V7a2 2 0 00-2-2H5zM5 14a2 2 0 00-2 2v3a1 1 0 001 1h1a1 1 0 001-1v-3a2 2 0 00-2-2H5z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-white mb-2">Aucune réservation trouvée</h3>
            <p class="text-gray-400 mb-6">
                @if($selectedStatus)
                    Aucune réservation avec le statut "{{ $statusLabel ?? $selectedStatus }}" trouvée.
                @else
                    Vous n'avez pas encore effectué de réservation.
                @endif
            </p>
            <a href="{{ route('films.index') }}" class="inline-flex items-center px-6 py-3 bg-cinema-gold hover:bg-yellow-500 text-black font-medium rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Réserver une séance
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($reservations->hasPages())
    <div class="mt-6">
        {{ $reservations->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
