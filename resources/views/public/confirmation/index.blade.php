@extends('layouts.cinema')

@section('title', 'Confirmation de réservation')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(!$reservation)
        <!-- Erreur : pas de réservation -->
        <div class="text-center py-16">
            <div class="bg-red-900 rounded-xl p-8 border border-red-800">
                <svg class="mx-auto h-16 w-16 text-red-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <h2 class="text-2xl font-bold text-white mb-4">Aucune réservation confirmée</h2>
                <p class="text-red-200 mb-6">
                    Nous n'avons trouvé aucune réservation récente à afficher
                </p>
                <div class="flex gap-4 justify-center">
                    <a href="{{ route('films.index') }}"
                       class="px-6 py-3 bg-gold text-black font-medium rounded-lg hover:bg-yellow-500 transition-colors">
                        Voir les films
                    </a>
                    <a href="{{ route('reservation.index') }}"
                       class="px-6 py-3 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-500 rounded-lg font-medium transition-colors">
                        Nouvelle réservation
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Success Header -->
        <div class="text-center mb-12">
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                <span class="text-gold">Réservation confirmée</span> !
            </h1>
            <p class="text-xl text-gray-300">Votre paiement a été traité avec succès</p>
            <p class="text-gray-400 mt-2">
                Numéro de réservation: <span class="text-gold font-mono">{{ $reservation->numero_reservation }}</span>
            </p>
        </div>

        <!-- Récapitulatif de la réservation -->
        <div class="bg-gray-900 rounded-xl p-8 border border-gray-800 mb-8">
            <h2 class="text-2xl font-bold text-white mb-6 text-center">Détails de votre réservation</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Informations du film -->
                <div class="space-y-4">
                    <div class="bg-gray-800 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">{{ $reservation->film_info['titre'] ?? 'Film' }}</h3>
                        <div class="space-y-3 text-gray-300">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <div class="font-medium">
                                        {{ $reservation->date_seance?->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                                    </div>
                                    <div class="text-sm text-gray-400">
                                        {{ $reservation->date_seance?->format('H:i') }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <div>
                                    <div class="font-medium">{{ $reservation->seance_info['salle'] ?? 'Salle' }}</div>
                                    <div class="text-sm text-gray-400">{{ $reservation->nb_places }} place(s)</div>
                                    @if($reservation->places_reservees && count($reservation->places_reservees) > 0)
                                        <div class="text-sm text-gray-400">Places: {{ implode(', ', $reservation->places_reservees) }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                <div>
                                    <div class="font-medium text-gold">{{ number_format($reservation->total, 2) }}€</div>
                                    <div class="text-sm text-gray-400">Total payé</div>
                                </div>
                            </div>

                            @if($reservation->client_info && isset($reservation->client_info['email']))
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                    <div>
                                        <div class="font-medium">{{ $reservation->client_info['prenom'] ?? '' }} {{ $reservation->client_info['nom'] ?? '' }}</div>
                                        <div class="text-sm text-gray-400">{{ $reservation->client_info['email'] }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- QR Code et billets -->
                <div class="space-y-4">
                    <div class="bg-gray-800 rounded-lg p-6 text-center">
                        <h3 class="text-lg font-semibold text-white mb-4">Vos billets électroniques</h3>

                        <!-- QR Code simulé -->
                        <div class="w-48 h-48 bg-white mx-auto rounded-lg flex items-center justify-center mb-4">
                            <div class="grid grid-cols-8 gap-1">
                                @for($i = 0; $i < 64; $i++)
                                    @php $seed = crc32($reservation->numero_reservation . $i); @endphp
                                    <div class="w-2 h-2 {{ ($seed % 2) ? 'bg-black' : 'bg-white' }}"></div>
                                @endfor
                            </div>
                        </div>

                        <p class="text-sm text-gray-300 mb-4">
                            Présentez ce QR code à l'entrée du cinéma
                        </p>

                        <a href="{{ route('qr.show', $reservation->numero_reservation) }}"
                           class="bg-gold hover:bg-yellow-500 text-black px-4 py-2 rounded-lg inline-block font-mono text-sm transition-colors">
                            {{ $reservation->numero_reservation }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="mt-8 bg-blue-900/20 border border-blue-700 rounded-lg p-4">
                <div class="flex gap-3">
                    <svg class="w-6 h-6 text-blue-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-blue-100">
                        <h4 class="font-semibold mb-2">Instructions importantes :</h4>
                        <ul class="text-sm space-y-1 list-disc list-inside ml-4">
                            <li>Arrivez au moins 15 minutes avant le début de la séance</li>
                            <li>Présentez votre QR code ou numéro de réservation à l'accueil</li>
                            @if($reservation->client_info && isset($reservation->client_info['email']))
                                <li>Un email de confirmation a été envoyé à {{ $reservation->client_info['email'] }}</li>
                            @endif
                            <li>En cas de problème, contactez notre service client</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('home') }}"
               class="px-8 py-3 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-500 rounded-lg font-medium transition-colors">
                Retour à l'accueil
            </a>
            <a href="{{ route('reservation.index') }}"
               class="px-8 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-medium transition-colors">
                Nouvelle réservation
            </a>
            <a href="{{ route('ticket.download', $reservation->numero_reservation) }}"
               class="px-8 py-3 bg-gold hover:bg-yellow-500 text-black font-medium rounded-lg transition-colors inline-block text-center">
                Télécharger PDF
            </a>
        </div>

        <!-- Informations supplémentaires -->
        <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-gray-900 rounded-xl p-6 border border-gray-800">
                <h3 class="text-lg font-semibold text-white mb-4">Informations pratiques</h3>
                <div class="space-y-3 text-sm text-gray-300">
                    <div>
                        <strong class="text-white">Adresse :</strong><br>
                        {{ $reservation->cinema_info['adresse'] ?? '123 Avenue du Cinéma, 75001 Paris' }}
                    </div>
                    <div>
                        <strong class="text-white">Parking :</strong><br>
                        Gratuit pendant 3h avec ticket de cinéma
                    </div>
                    <div>
                        <strong class="text-white">Transports :</strong><br>
                        Métro ligne 1 - Station République
                    </div>
                </div>
            </div>

            <div class="bg-gray-900 rounded-xl p-6 border border-gray-800">
                <h3 class="text-lg font-semibold text-white mb-4">Besoin d'aide ?</h3>
                <div class="space-y-3 text-sm text-gray-300">
                    <div>
                        <strong class="text-white">Service client :</strong><br>
                        📞 01 23 45 67 89<br>
                        ✉️ contact@cinephoria.fr
                    </div>
                    <div>
                        <strong class="text-white">Horaires :</strong><br>
                        Lun-Dim : 10h00 - 22h00
                    </div>
                    <div>
                        <strong class="text-white">Modification/Annulation :</strong><br>
                        Possible jusqu'à 2h avant la séance
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .print-section, .print-section * {
        visibility: visible;
    }
    .print-section {
        position: absolute;
        left: 0;
        top: 0;
    }
}
</style>
@endpush