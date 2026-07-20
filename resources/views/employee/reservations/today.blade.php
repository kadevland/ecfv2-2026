@extends('layouts.employee')

@section('title', 'Réservations du jour')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold leading-tight text-gray-900">
                🎫 Réservations du jour
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                {{ $date->locale('fr')->isoFormat('dddd D MMMM YYYY') }} • {{ $cinema_name }}
            </p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-500">
                {{ $stats['total'] }} réservation(s) • {{ number_format($stats['revenue_total'], 2) }}€
            </div>
            <a href="{{ route('employee.dashboard') }}" class="bg-white border border-gray-300 rounded-md shadow-sm px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                ← Dashboard
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Payées</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['payees'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Confirmées</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['confirmees'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Places vendues</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['places_vendues'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-600 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Revenus</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($stats['revenue_total'], 0) }}€</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres par statut -->
    <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Filtrer par statut
                </h3>
                <div class="flex space-x-2" x-data="{ activeFilter: 'all' }">
                    <button @click="activeFilter = 'all'"
                            :class="activeFilter === 'all' ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:text-gray-700'"
                            class="px-3 py-2 rounded-md text-sm font-medium transition-colors"
                            onclick="showAllReservations()">
                        Toutes ({{ $stats['total'] }})
                    </button>
                    <button @click="activeFilter = 'payee'"
                            :class="activeFilter === 'payee' ? 'bg-green-100 text-green-700' : 'text-gray-500 hover:text-gray-700'"
                            class="px-3 py-2 rounded-md text-sm font-medium transition-colors"
                            onclick="filterReservations('payee')">
                        Payées ({{ $stats['payees'] }})
                    </button>
                    <button @click="activeFilter = 'confirmee'"
                            :class="activeFilter === 'confirmee' ? 'bg-yellow-100 text-yellow-700' : 'text-gray-500 hover:text-gray-700'"
                            class="px-3 py-2 rounded-md text-sm font-medium transition-colors"
                            onclick="filterReservations('confirmee')">
                        Confirmées ({{ $stats['confirmees'] }})
                    </button>
                    @if($stats['annulees'] > 0)
                        <button @click="activeFilter = 'annulee'"
                                :class="activeFilter === 'annulee' ? 'bg-red-100 text-red-700' : 'text-gray-500 hover:text-gray-700'"
                                class="px-3 py-2 rounded-md text-sm font-medium transition-colors"
                                onclick="filterReservations('annulee')">
                            Annulées ({{ $stats['annulees'] }})
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des réservations -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Liste des réservations
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                Toutes les réservations pour {{ $date->locale('fr')->isoFormat('dddd D MMMM') }}
            </p>
        </div>

        <ul class="divide-y divide-gray-200">
            @forelse($reservations as $reservation)
                <li class="px-4 py-4 hover:bg-gray-50 reservation-item" data-status="{{ $reservation['statut'] }}">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <!-- Statut -->
                            <div class="flex-shrink-0">
                                @if($reservation['statut'] === 'payee')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ✓ Payée
                                    </span>
                                @elseif($reservation['statut'] === 'confirmee')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        ⏳ Confirmée
                                    </span>
                                @elseif($reservation['statut'] === 'annulee')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        ❌ Annulée
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $reservation['statut'] }}
                                    </span>
                                @endif
                            </div>

                            <!-- Informations réservation -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-3">
                                    <h4 class="text-base font-medium text-gray-900">
                                        {{ $reservation['numero_reservation'] }}
                                    </h4>
                                    <span class="text-sm text-gray-500">
                                        {{ $reservation['date_reservation']->format('H:i') }}
                                    </span>
                                </div>
                                <div class="mt-1 text-sm text-gray-600">
                                    <span class="font-medium">{{ $reservation['client_info']['prenom'] ?? 'Client' }} {{ $reservation['client_info']['nom'] ?? '' }}</span>
                                    @if(isset($reservation['client_info']['email']))
                                        • {{ $reservation['client_info']['email'] }}
                                    @endif
                                </div>
                                <div class="mt-1 text-sm text-gray-500">
                                    🎬 {{ $reservation['film_info']['titre'] ?? 'Film non défini' }}
                                    • ⏰ {{ $reservation['date_seance']->format('H:i') }}
                                </div>
                            </div>
                        </div>

                        <!-- Détails et montant -->
                        <div class="flex items-center space-x-6">
                            <!-- Places -->
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ $reservation['nb_places'] }}
                                </div>
                                <div class="text-xs text-gray-500">place(s)</div>
                            </div>

                            <!-- Montant -->
                            <div class="text-center">
                                <div class="text-lg font-semibold text-gray-900">
                                    {{ number_format($reservation['total'], 2) }}€
                                </div>
                                <div class="text-xs text-gray-500">montant</div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center space-x-2">
                                <!-- Voir détails -->
                                <button class="text-blue-600 hover:text-blue-900 text-sm font-medium"
                                        onclick="showReservationDetails('{{ $reservation['numero_reservation'] }}')">
                                    👁️ Détails
                                </button>

                                @if($reservation['statut'] === 'confirmee')
                                    <!-- Marquer comme payée -->
                                    <button class="text-green-600 hover:text-green-900 text-sm font-medium ml-2"
                                            onclick="markAsPaid('{{ $reservation['numero_reservation'] }}')">
                                        💳 Encaisser
                                    </button>
                                @endif

                                @if(in_array($reservation['statut'], ['confirmee', 'payee']))
                                    <!-- Annuler -->
                                    <button class="text-red-600 hover:text-red-900 text-sm font-medium ml-2"
                                            onclick="cancelReservation('{{ $reservation['numero_reservation'] }}')">
                                        ❌ Annuler
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Détails étendus (masqués par défaut) -->
                    <div id="details-{{ $reservation['numero_reservation'] }}" class="mt-4 bg-gray-50 rounded-lg p-4 hidden">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <h5 class="font-medium text-gray-900 mb-2">Informations séance</h5>
                                <div class="space-y-1 text-gray-600">
                                    <div>🎬 {{ $reservation['film_info']['titre'] ?? 'N/A' }}</div>
                                    <div>📅 {{ $reservation['date_seance']->locale('fr')->isoFormat('dddd D MMMM') }}</div>
                                    <div>⏰ {{ $reservation['date_seance']->format('H:i') }}</div>
                                </div>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 mb-2">Contact client</h5>
                                <div class="space-y-1 text-gray-600">
                                    <div>👤 {{ $reservation['client_info']['prenom'] ?? 'N/A' }} {{ $reservation['client_info']['nom'] ?? '' }}</div>
                                    <div>📧 {{ $reservation['client_info']['email'] ?? 'N/A' }}</div>
                                    @if(isset($reservation['client_info']['telephone']))
                                        <div>📞 {{ $reservation['client_info']['telephone'] }}</div>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-900 mb-2">Détails réservation</h5>
                                <div class="space-y-1 text-gray-600">
                                    <div>🎫 {{ $reservation['numero_reservation'] }}</div>
                                    <div>💺 {{ $reservation['nb_places'] }} place(s)</div>
                                    <div>💰 {{ number_format($reservation['total'], 2) }}€</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-4 py-8">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune réservation</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Aucune réservation n'a été effectuée pour aujourd'hui.
                        </p>
                    </div>
                </li>
            @endforelse
        </ul>
    </div>

    <!-- Actions rapides -->
    <div class="mt-8 flex justify-center space-x-4">
        <a href="{{ route('employee.seances.today') }}" class="bg-blue-600 border border-transparent rounded-md shadow-sm px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
            🎬 Voir les séances
        </a>
        <a href="{{ route('employee.seances.week') }}" class="bg-purple-600 border border-transparent rounded-md shadow-sm px-4 py-2 text-sm font-medium text-white hover:bg-purple-700">
            📆 Planning semaine
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Filtrage des réservations
function filterReservations(status) {
    const items = document.querySelectorAll('.reservation-item');
    items.forEach(item => {
        if (status === 'all' || item.dataset.status === status) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function showAllReservations() {
    filterReservations('all');
}

// Affichage des détails
function showReservationDetails(reservationNumber) {
    const details = document.getElementById(`details-${reservationNumber}`);
    if (details.classList.contains('hidden')) {
        details.classList.remove('hidden');
    } else {
        details.classList.add('hidden');
    }
}

// Actions sur les réservations (simulées)
function markAsPaid(reservationNumber) {
    if (confirm(`Confirmer l'encaissement pour la réservation ${reservationNumber} ?`)) {
        alert(`Réservation ${reservationNumber} marquée comme payée`);
        
        location.reload();
    }
}

function cancelReservation(reservationNumber) {
    if (confirm(`Confirmer l'annulation de la réservation ${reservationNumber} ?`)) {
        alert(`Réservation ${reservationNumber} annulée`);
        
        location.reload();
    }
}
</script>
@endpush