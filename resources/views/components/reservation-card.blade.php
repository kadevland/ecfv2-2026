@props(['reservation'])

<div class="p-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <!-- Contenu principal -->
        <div class="flex-1">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex-1">
                    <!-- Film avec affiche -->
                    <div class="flex gap-4 mb-4">
                        @if($reservation->film_affiche)
                        <img src="{{ $reservation->film_affiche }}"
                             alt="{{ $reservation->film_titre }}"
                             class="w-20 h-28 object-cover rounded-lg">
                        @endif
                        <div>
                            <h3 class="text-xl font-semibold text-white mb-1">
                                {{ $reservation->film_titre }}
                            </h3>
                            <div class="flex items-center gap-3 text-sm text-gray-400 mb-2">
                                <span class="bg-gray-800 px-2 py-1 rounded text-xs">
                                    {{ \App\Domain\Cinema\Enums\ClassificationFilmEnum::tryFrom($reservation->film_classification)?->label() }}
                                </span>
                                <span>{{ $reservation->film_duree }}</span>
                                <span class="text-gold">{{ $reservation->seance_version }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Informations détaillées -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <!-- Colonne 1 -->
                        <div class="space-y-3">
                            <!-- Cinéma et Salle -->
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-gold flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <div>
                                    <div class="text-white font-medium">{{ $reservation->cinema_nom }}</div>
                                    <div class="text-gray-400">{{ $reservation->cinema_ville }} - {{ $reservation->salle_nom }}</div>
                                </div>
                            </div>

                            <!-- Date et heure séance -->
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-gold flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v16z" />
                                </svg>
                                <div>
                                    <div class="text-white font-medium">Séance : {{ $reservation->seance_date }} à {{ $reservation->seance_heure }}</div>
                                    <div class="text-gray-400">Réservé le {{ $reservation->created_at->format('d/m/Y à H:i') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Colonne 2 -->
                        <div class="space-y-3">
                            <!-- Prix et places -->
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-gold flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                </svg>
                                <div>
                                    <div class="text-white font-medium">{{ $reservation->prix_total_formate }}</div>
                                    <div class="text-gray-400">{{ $reservation->places_formate }}</div>
                                    @if($reservation->sieges_formate)
                                    <div class="text-gray-400 text-xs mt-1">{{ $reservation->sieges_formate }}</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Numéro de réservation -->
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-gold flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                                <div>
                                    <div class="text-gray-400 text-xs">Numéro de réservation</div>
                                    <div class="font-mono text-gold font-medium">{{ $reservation->numero_reservation }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statut Badge -->
                <div class="flex-shrink-0">
                    @php
                        $statusClass = match($reservation->statut ?? 'INCONNUE') {
                            'EN_ATTENTE_PAIEMENT' => 'bg-yellow-900 text-yellow-200 border-yellow-700',
                            'CONFIRMEE' => 'bg-green-900 text-green-200 border-green-700',
                            'PAYEE' => 'bg-blue-900 text-blue-200 border-blue-700',
                            'UTILISEE' => 'bg-gray-900 text-gray-300 border-gray-700',
                            'ANNULEE' => 'bg-red-900 text-red-200 border-red-700',
                            'EXPIREE' => 'bg-orange-900 text-orange-200 border-orange-700',
                            default => 'bg-gray-900 text-gray-300 border-gray-700'
                        };
                        $statusLabel = match($reservation->statut ?? 'INCONNUE') {
                            'EN_ATTENTE_PAIEMENT' => 'En attente paiement',
                            'CONFIRMEE' => 'Confirmée',
                            'PAYEE' => 'Payée',
                            'UTILISEE' => 'Utilisée',
                            'ANNULEE' => 'Annulée',
                            'EXPIREE' => 'Expirée',
                            default => 'Statut inconnu'
                        };
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-2 min-w-0 sm:min-w-fit">
            @if($reservation->numero_reservation && in_array($reservation->statut, ['CONFIRMEE', 'PAYEE']))
                <a href="{{ route('qr.show', $reservation->numero_reservation) }}"
                   class="px-4 py-2 bg-cinema-gold hover:bg-yellow-500 text-black text-sm font-medium rounded-lg transition-colors text-center">
                    <span class="sm:hidden">QR Code</span>
                    <span class="hidden sm:inline">Voir QR Code</span>
                </a>
            @endif

            @if($reservation->numero_reservation)
                <a href="{{ route('ticket.download', $reservation->numero_reservation) }}"
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium rounded-lg transition-colors text-center">
                    <span class="sm:hidden">PDF</span>
                    <span class="hidden sm:inline">Télécharger PDF</span>
                </a>
            @endif
        </div>
    </div>
</div>