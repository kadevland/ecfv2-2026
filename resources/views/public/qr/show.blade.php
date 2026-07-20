@extends('layouts.cinema')

@section('title', 'QR Code - Billet Électronique')

@section('content')
<div class="min-h-screen bg-zinc-950 flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- QR Code Card -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-gold to-yellow-500 p-6 text-center">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-gold" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h1 class="text-xl font-bold text-black">Cinéphoria</h1>
                </div>
                <p class="text-black font-medium">Billet Électronique</p>
            </div>

            <!-- QR Code -->
            <div class="p-6 text-center">
                <div class="w-40 h-40 mx-auto mb-4 bg-white border-2 border-gray-200 rounded-lg flex items-center justify-center">
                    <!-- QR Code simulé -->
                    <div class="grid grid-cols-12 gap-px p-2">
                        @for($i = 0; $i < 144; $i++)
                            <div class="w-1 h-1 {{ rand(0, 1) ? 'bg-black' : 'bg-white' }}"></div>
                        @endfor
                    </div>
                </div>

                <div class="bg-gray-100 rounded-lg px-3 py-2 font-mono text-sm text-gray-800 mb-4">
                    {{ $reservationNumber }}
                </div>

                <p class="text-xs text-gray-500 mb-6">
                    Présentez ce QR code à l'entrée du cinéma
                </p>
            </div>

            <!-- Informations de la séance -->
            <div class="border-t border-gray-200 p-6 space-y-4">
                <!-- Film -->
                <div class="text-center mb-4">
                    <h2 class="text-lg font-bold text-gray-900 mb-1">{{ $reservation['film_titre'] }}</h2>
                    <p class="text-sm text-gray-600">{{ $reservation['film_genre'] }} • {{ $reservation['film_duree'] }}min</p>
                </div>

                <!-- Détails séance -->
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500 uppercase text-xs font-medium mb-1">Date & Heure</div>
                        <div class="text-gray-900 font-medium">
                            {{ \Carbon\Carbon::parse($reservation['date_seance'])->format('d/m/Y') }}
                        </div>
                        <div class="text-gray-700">
                            {{ \Carbon\Carbon::parse($reservation['date_seance'])->format('H:i') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-gray-500 uppercase text-xs font-medium mb-1">Salle</div>
                        <div class="text-gray-900 font-medium">{{ $reservation['salle'] }}</div>
                    </div>
                </div>

                <!-- Places -->
                <div>
                    <div class="text-gray-500 uppercase text-xs font-medium mb-2">Places réservées</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($reservation['places'] as $place)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gold text-black">
                                {{ $place }}
                            </span>
                        @endforeach
                    </div>
                    <div class="text-sm text-gray-600 mt-1">
                        {{ $reservation['nb_places'] }} place{{ $reservation['nb_places'] > 1 ? 's' : '' }}
                    </div>
                </div>

                <!-- Cinéma -->
                <div>
                    <div class="text-gray-500 uppercase text-xs font-medium mb-1">Cinéma</div>
                    <div class="text-gray-900 font-medium">{{ $reservation['cinema_nom'] }}</div>
                    <div class="text-gray-600 text-sm">{{ $reservation['cinema_adresse'] }}</div>
                </div>

                <!-- Statut -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <div>
                        <div class="text-gray-500 uppercase text-xs font-medium">Statut</div>
                        <div class="flex items-center gap-2 mt-1">
                            @if($reservation['statut'] === 'confirmee')
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <span class="text-green-700 font-medium text-sm">Confirmée</span>
                            @elseif($reservation['statut'] === 'terminee')
                                <div class="w-2 h-2 bg-gray-500 rounded-full"></div>
                                <span class="text-gray-700 font-medium text-sm">Terminée</span>
                            @else
                                <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                <span class="text-yellow-700 font-medium text-sm">En attente</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-gray-500 uppercase text-xs font-medium">Total</div>
                        <div class="text-lg font-bold text-gray-900">{{ number_format($reservation['total'], 2) }}€</div>
                    </div>
                </div>
            </div>

            <!-- Footer avec instructions -->
            <div class="bg-gray-50 p-4 text-center border-t border-gray-200">
                <div class="text-xs text-gray-600 space-y-1">
                    <div class="font-medium mb-2">Instructions importantes :</div>
                    <div>• Arrivez 15 minutes avant la séance</div>
                    <div>• Présentez ce billet à l'accueil</div>
                    <div>• Gardez votre billet pendant toute la séance</div>
                </div>
            </div>

            <!-- Actions -->
            <div class="p-4 space-y-3">
                <a href="{{ route('ticket.download', $reservationNumber) }}"
                   class="w-full bg-gold hover:bg-yellow-500 text-black font-medium py-3 px-4 rounded-lg transition-colors block text-center">
                    Télécharger PDF
                </a>
                <button onclick="window.print()"
                        class="w-full bg-gray-700 hover:bg-gray-600 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                    Imprimer cette page
                </button>
                <a href="{{ route('home') }}"
                   class="block w-full text-center border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium py-3 px-4 rounded-lg transition-colors">
                    Retour à l'accueil
                </a>
            </div>
        </div>

        <!-- Informations complémentaires -->
        <div class="mt-6 text-center">
            <div class="bg-gray-900 rounded-xl p-4 border border-gray-800">
                <h3 class="text-lg font-semibold text-white mb-3">Informations pratiques</h3>
                <div class="text-sm text-gray-300 space-y-2">
                    <div class="flex items-center justify-between">
                        <span>Service client :</span>
                        <span class="text-gold">01 23 45 67 89</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Horaires cinéma :</span>
                        <span>10h00 - 23h00</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Parking :</span>
                        <span>Gratuit 3h</span>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-blue-900/20 border border-blue-700 rounded-lg">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-xs text-blue-100">
                            <strong>COVID-19 :</strong> Port du masque recommandé. Désinfection des mains à l'entrée.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .print-area, .print-area * {
        visibility: visible;
    }
    .print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
}

.print-area {
    /* Styles pour l'impression */
}
</style>
@endpush

@push('scripts')
<script>
// Auto-refresh du QR code toutes les 30 secondes pour éviter l'expiration
setInterval(function() {
    // Simuler un refresh léger du QR code
    const qrContainer = document.querySelector('.grid-cols-12');
    if (qrContainer) {
        // Animation subtile pour indiquer le refresh
        qrContainer.style.opacity = '0.8';
        setTimeout(() => {
            qrContainer.style.opacity = '1';
        }, 200);
    }
}, 30000);

// Fonction pour partager le QR code (si support)
function shareQRCode() {
    if (navigator.share) {
        navigator.share({
            title: 'Mon billet Cinéphoria',
            text: 'Réservation {{ $reservationNumber }} - {{ $reservation["film_titre"] }}',
            url: window.location.href
        });
    }
}

// Alerte avant fermeture de la page
window.addEventListener('beforeunload', function(e) {
    if (document.referrer === '') {
        e.preventDefault();
        e.returnValue = 'Êtes-vous sûr de vouloir quitter ? Gardez cette page ouverte pour accéder à votre billet.';
    }
});
</script>
@endpush