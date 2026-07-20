@extends('layouts.cinema')

@section('title', 'Paiement')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(!$reservation)
        <!-- Erreur : pas de réservation -->
        <div class="text-center py-16">
            <div class="bg-red-900 rounded-xl p-8 border border-red-800">
                <svg class="mx-auto h-16 w-16 text-red-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <h2 class="text-2xl font-bold text-white mb-4">Aucune réservation en cours</h2>
                <p class="text-red-200 mb-6">
                    Vous devez d'abord sélectionner une séance et vos places
                </p>
                <div class="flex gap-4 justify-center">
                    <a href="{{ route('films.index') }}"
                       class="px-6 py-3 bg-gold text-black font-medium rounded-lg hover:bg-yellow-500 transition-colors">
                        Voir les films
                    </a>
                    <a href="{{ route('checkout') }}"
                       class="px-6 py-3 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-500 rounded-lg font-medium transition-colors">
                        Retour au panier
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Paiement avec réservation -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Header -->
            <div class="lg:col-span-2 text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                    <span class="text-gold">Paiement</span> sécurisé
                </h1>
                <p class="text-gray-300">Finalisez votre réservation pour {{ $reservation->film_info['titre'] }}</p>
            </div>

            <!-- Messages de feedback -->
            @if(session('error'))
                <div class="lg:col-span-2 bg-red-900 border border-red-700 text-red-300 px-4 py-3 rounded-lg mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Récapitulatif de commande -->
            <div class="bg-gray-900 rounded-xl p-6 border border-gray-800 h-fit">
                <h2 class="text-xl font-bold text-white mb-4">Récapitulatif</h2>

                <div class="space-y-4">
                    <div class="bg-gray-800 rounded-lg p-4">
                        <h3 class="font-semibold text-white mb-2">{{ $reservation->film_info['titre'] }}</h3>
                        <div class="space-y-1 text-sm text-gray-300">
                            <div class="flex justify-between">
                                <span>Date & Heure:</span>
                                <span>{{ $reservation->date_seance?->format('d/m/Y à H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Salle:</span>
                                <span>{{ $reservation->seance_info['salle'] ?? 'Non définie' }}</span>
                            </div>
                            @if($reservation->places_reservees && count($reservation->places_reservees) > 0)
                                <div class="flex justify-between">
                                    <span>Places:</span>
                                    <span>{{ implode(', ', $reservation->places_reservees) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="border-t border-gray-700 pt-4">
                        <div class="space-y-2 text-gray-300">
                            <div class="flex justify-between">
                                <span>{{ $reservation->nb_places }} place(s) × {{ number_format($reservation->tarif_unitaire, 2) }}€</span>
                                <span>{{ number_format($reservation->total, 2) }}€</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Frais de service</span>
                                <span>Gratuit</span>
                            </div>
                        </div>
                        <div class="border-t border-gray-700 mt-2 pt-2">
                            <div class="flex justify-between text-xl font-bold text-gold">
                                <span>Total</span>
                                <span>{{ number_format($reservation->total, 2) }}€</span>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code simulation -->
                    <div class="bg-gray-800 rounded-lg p-4 text-center">
                        <p class="text-sm text-gray-400 mb-2">Vos billets électroniques</p>
                        <div class="w-32 h-32 bg-white mx-auto rounded-lg flex items-center justify-center">
                            <div class="text-4xl">📱</div>
                        </div>
                        <p class="text-xs text-gray-400 mt-2">
                            Disponibles après paiement
                        </p>
                    </div>
                </div>
            </div>

            <!-- Formulaire de paiement -->
            <div class="bg-gray-900 rounded-xl p-6 border border-gray-800">
                <form action="{{ route('payment.process') }}" method="POST" x-data="paymentForm()">
                    @csrf

                    <h2 class="text-xl font-bold text-white mb-6">Informations de contact</h2>

                    <!-- Informations personnelles -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Prénom *</label>
                            <input type="text"
                                   name="prenom"
                                   value="{{ old('prenom', $reservation->client_info['prenom'] ?? '') }}"
                                   required
                                   class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent"
                                   placeholder="Votre prénom">
                            @error('prenom')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Nom *</label>
                            <input type="text"
                                   name="nom"
                                   value="{{ old('nom', $reservation->client_info['nom'] ?? '') }}"
                                   required
                                   class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent"
                                   placeholder="Votre nom">
                            @error('nom')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Email *</label>
                        <input type="email"
                               name="email"
                               value="{{ old('email', $reservation->client_info['email'] ?? '') }}"
                               required
                               class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent"
                               placeholder="votre@email.com">
                        @error('email')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-400 mt-1">Vos billets électroniques seront envoyés à cette adresse</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Téléphone</label>
                        <input type="tel"
                               name="telephone"
                               value="{{ old('telephone', $reservation->client_info['telephone'] ?? '') }}"
                               class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent"
                               placeholder="06 12 34 56 78">
                        @error('telephone')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Informations de paiement -->
                    <h3 class="text-lg font-semibold text-white mb-4 mt-8">Paiement par carte bancaire</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Numéro de carte</label>
                        <input type="text"
                               x-model="cardNumber"
                               @input="formatCardNumber()"
                               maxlength="19"
                               class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent"
                               placeholder="1234 5678 9012 3456">
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Date d'expiration</label>
                            <input type="text"
                                   x-model="expiryDate"
                                   @input="formatExpiryDate()"
                                   maxlength="5"
                                   class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent"
                                   placeholder="MM/AA">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Code de sécurité</label>
                            <input type="text"
                                   x-model="cvv"
                                   maxlength="4"
                                   class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent"
                                   placeholder="123">
                        </div>
                    </div>

                    <!-- Conditions -->
                    <div class="mb-6">
                        <label class="flex items-start gap-3">
                            <input type="checkbox"
                                   x-model="acceptTerms"
                                   class="mt-1 rounded border-gray-700 bg-gray-800 text-gold focus:ring-gold focus:ring-offset-0">
                            <span class="text-sm text-gray-300">
                                J'accepte les <a href="#" class="text-gold hover:text-yellow-400 underline">conditions générales de vente</a>
                                et la <a href="#" class="text-gold hover:text-yellow-400 underline">politique de confidentialité</a>
                            </span>
                        </label>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-4">
                        <a href="{{ route('checkout') }}"
                           class="flex-1 px-6 py-3 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-500 text-center rounded-lg font-medium transition-colors">
                            Retour
                        </a>
                        <button type="submit"
                                :disabled="!canPay()"
                                :class="canPay() ? 'hover:bg-yellow-500' : 'opacity-50 cursor-not-allowed'"
                                class="flex-1 px-6 py-3 bg-gold text-black font-bold rounded-lg transition-colors">
                            Payer {{ number_format($reservation->total, 2) }}€
                        </button>
                    </div>

                    <!-- Sécurité -->
                    <div class="mt-6 flex items-center justify-center gap-4 text-xs text-gray-400">
                        <div class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-9a2 2 0 00-2-2H6a2 2 0 00-2 2v9a2 2 0 002 2zm10-12V9a4 4 0 00-8 0v2h8z"></path>
                            </svg>
                            <span>Paiement sécurisé SSL</span>
                        </div>
                        <span>|</span>
                        <span>💳 Visa, Mastercard acceptées</span>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection

@if($reservation)
@push('scripts')
<script>
function paymentForm() {
    return {
        cardNumber: '',
        expiryDate: '',
        cvv: '',
        acceptTerms: false,

        formatCardNumber() {
            // Supprimer tous les espaces et ne garder que les chiffres
            let value = this.cardNumber.replace(/\s/g, '').replace(/[^0-9]/gi, '');

            // Ajouter un espace tous les 4 chiffres
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;

            this.cardNumber = formattedValue;
        },

        formatExpiryDate() {
            // Supprimer tous les caractères non numériques
            let value = this.expiryDate.replace(/[^0-9]/g, '');

            // Ajouter le slash après les 2 premiers chiffres
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }

            this.expiryDate = value;
        },

        canPay() {
            return this.cardNumber.length >= 16 &&
                   this.expiryDate.length === 5 &&
                   this.cvv.length >= 3 &&
                   this.acceptTerms;
        }
    }
}
</script>
@endpush
@endif