@extends('layouts.cinema')

@section('title', 'QR Code - ' . $reservation->numeroReservation)

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
            Votre <span class="text-gold">Billet</span>
        </h1>
        <p class="text-gray-300">Présentez ce QR Code à l'entrée du cinéma</p>
    </div>

    <!-- Ticket Card -->
    <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
        <!-- Film Info -->
        <div class="bg-gradient-to-r from-gold/20 to-transparent p-6 border-b border-gray-800">
            <h2 class="text-2xl font-bold text-white mb-2">{{ $reservation->filmTitre ?? 'Film' }}</h2>
            <div class="text-gray-300 space-y-1">
                <p>📅 {{ $reservation->seanceDate ? $reservation->seanceDate->format('d/m/Y à H:i') : 'Date à confirmer' }}</p>
                <p>🎬 {{ $reservation->salleName ?? 'Salle' }} - {{ $reservation->cinemaName ?? 'Cinéma' }}</p>
                <p>🎟️ {{ $reservation->nombrePlaces }} place(s)</p>
            </div>
        </div>

        <!-- QR Code Section -->
        <div class="p-8">
            <div class="bg-white rounded-lg p-6 text-center">
                <!-- QR Code SVG -->
                <div class="flex justify-center mb-4">
                    {!! $qrCodeSvg !!}
                </div>
                
                <!-- Reservation Number -->
                <div class="border-t pt-4">
                    <p class="text-gray-600 text-sm mb-1">Numéro de réservation</p>
                    <p class="text-2xl font-mono font-bold text-gray-900">{{ $reservation->numeroReservation }}</p>
                </div>
            </div>

            <!-- Status -->
            <div class="mt-6 text-center">
                @php
                    $statusClasses = match($reservation->statut) {
                        'payee' => 'bg-green-500',
                        'confirmee' => 'bg-blue-500',
                        'en_attente', 'en_attente_paiement' => 'bg-yellow-500',
                        'annulee' => 'bg-red-500',
                        default => 'bg-gray-500',
                    };
                    $statusLabel = match($reservation->statut) {
                        'payee' => 'Payée',
                        'confirmee' => 'Confirmée',
                        'en_attente', 'en_attente_paiement' => 'En attente de paiement',
                        'annulee' => 'Annulée',
                        default => ucfirst($reservation->statut),
                    };
                @endphp
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $statusClasses }} text-white">
                    {{ $statusLabel }}
                </span>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                <button onclick="window.print()" 
                        class="px-6 py-3 bg-gold text-black rounded-lg font-medium hover:bg-gold/90 transition-colors">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Imprimer
                </button>
                <a href="{{ route('account') }}" 
                   class="px-6 py-3 bg-gray-800 text-white rounded-lg font-medium hover:bg-gray-700 transition-colors text-center">
                    Retour à mon compte
                </a>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="bg-gray-800/50 px-6 py-4 text-center text-sm text-gray-400">
            <p>Ce billet est valable uniquement pour la séance indiquée</p>
            <p class="mt-1">Présentez-vous 15 minutes avant le début de la séance</p>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .bg-gray-900, .bg-gray-900 * {
            visibility: visible;
        }
        .bg-gray-900 {
            position: absolute;
            left: 0;
            top: 0;
            background: white !important;
            color: black !important;
        }
    }
</style>
@endpush
@endsection