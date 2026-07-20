@extends('layouts.admin')

@section('title', 'Gestion des Réservations')

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Réservations</span>
    </nav>
@endsection

@section('subtitle', 'Gérez toutes les réservations du système')

@section('content')
    <div class="space-y-6">
        <!-- Header with Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center space-x-3">
                <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Réservations</h1>
                    <p class="text-sm text-gray-600">{{ $total }} réservation(s) au total</p>
                </div>
            </div>

            <!-- Export/Action Buttons -->
            {{-- <div class="flex space-x-3">
                <button type="button" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exporter CSV
                </button>
            </div> --}}
        </div>

        <!-- Filters -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Filtres</h2>
            </div>
            <form method="GET" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Numéro de réservation -->
                    <div>
                        <label for="numero_reservation" class="block text-sm font-medium text-gray-700 mb-1">
                            Numéro de réservation
                        </label>
                        <input type="text"
                               id="numero_reservation"
                               name="numero_reservation"
                               value="{{ $filters['numero_reservation'] ?? '' }}"
                               placeholder="ex: RES123456"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Statut -->
                    <div>
                        <label for="statut" class="block text-sm font-medium text-gray-700 mb-1">
                            Statut
                        </label>
                        <select id="statut"
                                name="statut"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente" @if(($filters['statut'] ?? '') === 'en_attente') selected @endif>En attente de paiement</option>
                            <option value="confirmee" @if(($filters['statut'] ?? '') === 'confirmee') selected @endif>Confirmée</option>
                            <option value="payee" @if(($filters['statut'] ?? '') === 'payee') selected @endif>Payée</option>
                            <option value="annulee" @if(($filters['statut'] ?? '') === 'annulee') selected @endif>Annulée</option>
                            <option value="expiree" @if(($filters['statut'] ?? '') === 'expiree') selected @endif>Expirée</option>
                            <option value="utilisee" @if(($filters['statut'] ?? '') === 'utilisee') selected @endif>Utilisée</option>
                        </select>
                    </div>

                    <!-- Date début -->
                    <div>
                        <label for="date_debut" class="block text-sm font-medium text-gray-700 mb-1">
                            Date début
                        </label>
                        <input type="date"
                               id="date_debut"
                               name="date_debut"
                               value="{{ $filters['date_debut'] ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Date fin -->
                    <div>
                        <label for="date_fin" class="block text-sm font-medium text-gray-700 mb-1">
                            Date fin
                        </label>
                        <input type="date"
                               id="date_fin"
                               name="date_fin"
                               value="{{ $filters['date_fin'] ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-4">
                    <a href="{{ route('admin.reservations.index') }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Réinitialiser
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                        Appliquer les filtres
                    </button>
                </div>
            </form>
        </div>

        <!-- Reservations Table -->
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Liste des réservations</h2>
            </div>

            @if(count($reservations) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Numéro / Client
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Séance / Film
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Places
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Montant
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date création
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reservations as $reservation)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $reservation->numeroReservation }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $reservation->emailClient ?? $reservation->userEmail ?? 'Email non disponible' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <div class="font-medium">{{ $reservation->filmTitre ?? 'Film inconnu' }}</div>
                                            <div class="text-gray-500">
                                                {{ $reservation->seanceDate?->format('d/m/Y H:i') ?? 'Date inconnue' }}
                                            </div>
                                            <div class="text-gray-500 text-xs">
                                                {{ $reservation->salleName ?? 'Salle inconnue' }} - {{ $reservation->cinemaName ?? 'Cinéma inconnu' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $reservation->nombrePlaces }} place(s)
                                        </div>
                                        @if(false && $reservation->placesDetails && count($reservation->placesDetails) > 0)
                                            <div class="text-xs text-gray-500">
                                                Places: {{ implode(', ', array_map(fn($place) => ($place['rangee'] ?? 'A') . ($place['numero'] ?? '1'), $reservation->placesDetails)) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ number_format($reservation->montantTotal->getAmount() / 100, 2) }} €
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            // Mapper les valeurs de la BD vers les valeurs de l'enum pour les classes CSS
                                            try {
                                                $statutMapped = match(strtolower($reservation->statut)) {
                                                    'en_attente_paiement' => 'en_attente',
                                                    'confirmee' => 'confirmee',
                                                    'payee' => 'payee',
                                                    'annulee' => 'annulee',
                                                    'expiree' => 'expiree',
                                                    'utilisee' => 'utilisee',
                                                    'en_attente' => 'en_attente',
                                                    default => $reservation->statut
                                                };
                                                $statutEnum = \App\Domain\Enums\StatutReservation::from($statutMapped);
                                                $badgeClass = $statutEnum->getBadgeClass();
                                            } catch (\ValueError $e) {
                                                $badgeClass = 'bg-yellow-100 text-yellow-800'; // défaut pour EN_ATTENTE_PAIEMENT
                                            }
                                        @endphp
                                        @php
                                            // Mapping direct des labels pour affichage (cas insensitive)
                                            $statutDisplayLabel = match(strtolower($reservation->statut)) {
                                                'en_attente_paiement' => 'En attente',
                                                'confirmee' => 'Confirmée',
                                                'payee' => 'Payée',
                                                'annulee' => 'Annulée',
                                                'expiree' => 'Expirée',
                                                'utilisee' => 'Utilisée',
                                                'en_attente' => 'En attente',
                                                default => $reservation->statut
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                            {{ $statutDisplayLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $reservation->createdAt?->format('d/m/Y H:i') ?? 'Date inconnue' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('admin.reservations.show', $reservation->id) }}"
                                               class="text-blue-600 hover:text-blue-900 font-medium">
                                                Voir détails
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($pagination->hasMultiplePages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                @if($pagination->previousPage())
                                    <a href="{{ request()->fullUrlWithQuery(['page' => $pagination->previousPage()]) }}"
                                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Précédent
                                    </a>
                                @endif
                                @if($pagination->nextPage())
                                    <a href="{{ request()->fullUrlWithQuery(['page' => $pagination->nextPage()]) }}"
                                       class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Suivant
                                    </a>
                                @endif
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Affichage de
                                        <span class="font-medium">{{ $pagination->from() }}</span>
                                        à
                                        <span class="font-medium">{{ $pagination->to() }}</span>
                                        sur
                                        <span class="font-medium">{{ $pagination->total }}</span>
                                        résultats
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        @if($pagination->previousPage())
                                            <a href="{{ request()->fullUrlWithQuery(['page' => $pagination->previousPage()]) }}"
                                               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                <span class="sr-only">Précédent</span>
                                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </a>
                                        @endif

                                        @foreach($pagination->getPageRange() as $page)
                                            @if($page === $pagination->currentPage)
                                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue-50 text-sm font-medium text-blue-600">
                                                    {{ $page }}
                                                </span>
                                            @else
                                                <a href="{{ request()->fullUrlWithQuery(['page' => $page]) }}"
                                                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                                    {{ $page }}
                                                </a>
                                            @endif
                                        @endforeach

                                        @if($pagination->nextPage())
                                            <a href="{{ request()->fullUrlWithQuery(['page' => $pagination->nextPage()]) }}"
                                               class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                <span class="sr-only">Suivant</span>
                                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </a>
                                        @endif
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune réservation</h3>
                    <p class="mt-1 text-sm text-gray-500">Aucune réservation ne correspond à vos critères de recherche.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.reservations.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Voir toutes les réservations
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
