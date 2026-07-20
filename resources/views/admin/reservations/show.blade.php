@extends('layouts.admin')

@section('title', 'Détails de la Réservation')

@section('breadcrumbs')
    <nav class="flex space-x-2 text-sm text-gray-600">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-900">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.reservations.index') }}" class="hover:text-gray-900">Réservations</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">{{ $reservation->numeroReservation }}</span>
    </nav>
@endsection

@section('subtitle', 'Informations détaillées de la réservation')

@section('content')

    <div class="space-y-6">
        <!-- Header with Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center space-x-3">
                <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Réservation {{ $reservation->numeroReservation }}</h1>
                    <p class="text-sm text-gray-600">Créée le {{ $reservation->dateCreation->format('d/m/Y à H:i') }}</p>
                </div>
            </div>

            <!-- Status Badge -->
            <div>
                @php
                    $statusClasses = [
                        'en_attente' => 'bg-yellow-100 text-yellow-800',
                        'en_attente_paiement' => 'bg-yellow-100 text-yellow-800',
                        'confirmee' => 'bg-blue-100 text-blue-800',
                        'payee' => 'bg-green-100 text-green-800',
                        'annulee' => 'bg-red-100 text-red-800',
                        'expiree' => 'bg-gray-100 text-gray-800',
                    ];
                    $statusLabels = [
                        'en_attente' => 'En attente',
                        'en_attente_paiement' => 'En attente',
                        'confirmee' => 'Confirmée',
                        'payee' => 'Payée',
                        'annulee' => 'Annulée',
                        'expiree' => 'Expirée',
                    ];

                @endphp
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses[$reservation->statut] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ $statusLabels[$reservation->statut] ?? $reservation->statut }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informations générales -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Informations générales</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de réservation</label>
                                <p class="text-sm text-gray-900 font-mono bg-gray-50 px-3 py-2 rounded-md">
                                    {{ $reservation->numeroReservation }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email client</label>
                                <p class="text-sm text-gray-900">{{ $reservation->userEmail }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de places</label>
                                <p class="text-sm text-gray-900">{{ $reservation->nombrePlaces }} place(s)</p>

                                @if ($reservation->placesDetails && count($reservation->placesDetails) > 0)
                                    <div class="mt-2">
                                        <span class="text-sm font-medium text-gray-700">Places sélectionnées:</span>
                                        <ul class="text-sm text-gray-900 list-disc list-inside mt-1">
                                            @foreach ($reservation->placesDetails as $type => $nbre)
                                            <li>{{ ucfirst($type) }} : {{ $nbre }}</li>
                                                {{-- <li>{{ $place['rangee'] ?? 'A' }}{{ $place['numero'] ?? '1' }}
                                                    ({{ ucfirst($place['type'] ?? 'normal') }})</li> --}}
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif

                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Montant total</label>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ number_format($reservation->montantTotal->getAmount() / 100, 2) }} €</p>
                            </div>
                        </div>

                        @if ($reservation->dateExpiration)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date d'expiration</label>
                                <p class="text-sm text-gray-900">
                                    {{ $reservation->dateExpiration->format('d/m/Y à H:i') }}
                                    @if ($reservation->dateExpiration < now())
                                        <span class="ml-2 text-red-600 text-xs">(Expirée)</span>
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informations de la séance -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Informations de la séance</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-start space-x-4">
                            <div class="w-16 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                @if ($reservation->filmAffcheUrl)
                                    <img src="{{ $reservation->filmAffcheUrl }}"
                                        alt="Affiche de {{ $reservation->filmTitre }}"
                                        class="w-full h-full object-cover rounded-lg">
                                @else
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                @endif


                            </div>
                            <div class="flex-1 space-y-2">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $reservation->filmTitre }}</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="font-medium text-gray-700">Date et heure:</span>
                                        <p class="text-gray-900">
                                            {{ $reservation->seanceDate ? $reservation->seanceDate->format('d/m/Y à H:i') : 'Date inconnue' }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Salle:</span>
                                        <p class="text-gray-900">{{ $reservation->salleName }}</p>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Cinéma:</span>
                                        <p class="text-gray-900">{{ $reservation->cinemaName }}</p>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-700">Client:</span>
                                        <p class="text-gray-900">{{ $reservation->userNom }}
                                            {{ $reservation->userPrenom }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Places sélectionnées -->
                {{-- @if ($reservation->placesDetails && count($reservation->placesDetails) > 0)
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-800">Places sélectionnées</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                                @foreach ($reservation->placesDetails as $place)
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg px-3 py-2 text-center">
                                        <div class="text-sm font-medium text-blue-900">
                                            {{ $place['rangee'] ?? 'A' }}{{ $place['numero'] ?? '1' }}
                                        </div>
                                        <div class="text-xs text-blue-600">
                                            {{ ucfirst($place['type'] ?? 'normal') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif --}}

                <!-- Détail des tarifs -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Détail des tarifs</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2">
                                <div>
                                    <span class="text-sm font-medium text-gray-900">Réservation</span>
                                    <span class="text-sm text-gray-500 ml-2">({{ $reservation->nombrePlaces }}
                                        place(s))</span>
                                </div>
                                <div class="text-sm text-gray-900">
                                    <span
                                        class="font-medium">{{ number_format($reservation->montantTotal->getAmount() / 100, 2) }}
                                        €</span>
                                </div>
                            </div>
                            <div class="pt-3 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-base font-semibold text-gray-900">Total TTC</span>
                                    <span
                                        class="text-lg font-bold text-gray-900">{{ number_format($reservation->montantTotal->getAmount() / 100, 2) }}
                                        €</span>
                                </div>
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-sm text-gray-600">Total HT</span>
                                    <span
                                        class="text-sm text-gray-900">{{ number_format($reservation->montantHt->getAmount() / 100, 2) }}
                                        €</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Actions -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Actions</h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('admin.reservations.index') }}"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Retour à la liste
                        </a>

                        @if ($reservation->statut === 'payee')
                            <button type="button"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Télécharger les billets
                            </button>
                        @endif

                        @if (in_array($reservation->statut, ['en_attente', 'confirmee']))
                            <button type="button"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Annuler la réservation
                            </button>
                        @endif
                    </div>
                </div>

                <!-- QR Code -->
                {{-- @if ($reservation->qrCode)
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-800">QR Code</h2>
                        </div>
                        <div class="p-6 text-center">
                            <div class="bg-gray-100 p-4 rounded-lg inline-block">
                                <p class="text-sm text-gray-600 font-mono">{{ $reservation->qrCode }}</p>
                            </div>
                        </div>
                    </div>
                @endif --}}

                <!-- Historique -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Historique</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex items-start space-x-3">
                                <div class="w-2 h-2 bg-blue-600 rounded-full mt-2"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Réservation créée</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $reservation->dateCreation->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>

                            @if ($reservation->dateModification > $reservation->dateCreation)
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-green-600 rounded-full mt-2"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Dernière modification</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $reservation->dateModification->format('d/m/Y à H:i') }}</p>
                                    </div>
                                </div>
                            @endif

                            @if ($reservation->statut === 'annulee')
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 bg-red-600 rounded-full mt-2"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Réservation annulée</p>
                                        <p class="text-xs text-gray-500">Statut: {{ $reservation->statut }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
