@extends('layouts.cinema')

@section('title', 'Sélection des places')

@section('content')
<div class="bg-black min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="reservationApp()" x-init="init()">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                Réserver vos <span class="text-gold">places</span>
            </h1>
            <p class="text-gray-300">{{ $seance['titre'] }}</p>
        </div>

        <!-- Infos séance -->
        <div class="bg-gray-900/95 backdrop-blur-sm rounded-xl p-6 mb-8 border border-gray-800">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex gap-4">
                    <!-- Poster miniature -->
                    @if(isset($seance['poster_url']) && $seance['poster_url'])
                        <div class="flex-shrink-0">
                            <img src="{{ $seance['poster_url'] }}"
                                 alt="{{ $seance['titre'] }}"
                                 class="w-20 h-28 rounded-lg shadow-lg object-cover">
                        </div>
                    @endif

                    <div class="flex-1">
                        <h2 class="text-xl font-bold text-white mb-4">{{ $seance['titre'] }}</h2>
                        <div class="space-y-2 text-gray-300">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z"></path>
                                </svg>
                                <span>{{ \Carbon\Carbon::parse($seance['date_heure'])->format('d/m/Y à H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                </svg>
                                <span>{{ $seance['cinema_nom'] }} • {{ $seance['salle'] }}</span>
                            </div>
                            <div class="flex items-center gap-2 mt-4">
                                <span class="text-lg font-semibold text-gold">{{ number_format($seance['tarif'], 2) }}€ par place</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-white mb-3">Récapitulatif</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-gray-300">
                            <span>Places sélectionnées:</span>
                            <span x-text="selectedCount"></span>
                        </div>
                        <div class="flex justify-between text-gray-300">
                            <span>Prix unitaire:</span>
                            <span>{{ number_format($seance['tarif'], 2) }}€</span>
                        </div>
                        <div class="flex justify-between text-gray-300">
                            <span>Type de salle:</span>
                            <span>{{ $seance['salle_type'] === 'numerotee' ? 'Places numérotées' : 'Placement libre' }}</span>
                        </div>
                        <div class="border-t border-gray-700 pt-2 mt-2">
                            <div class="flex justify-between text-xl font-bold text-gold">
                                <span>Total:</span>
                                <span x-text="totalPrice + '€'"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire de réservation -->
        <form method="POST" action="{{ route('reservation.create') }}" class="space-y-8">
            @csrf
            <input type="hidden" name="seance_id" value="{{ $seance['seance_id'] }}">

            <!-- Sélection du nombre de places par tarif -->
            <div class="bg-gray-900/95 backdrop-blur-sm rounded-xl p-6 border border-gray-800">
                <h3 class="text-xl font-semibold text-white mb-6">Sélection des places</h3>

                <div class="space-y-4">
                    @if(isset($seance['tarifs']['normal']))
                    <!-- Tarif Normal -->
                    <div class="bg-gray-800 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-white font-medium">Tarif Normal</label>
                            <span class="text-gold font-semibold">{{ number_format($seance['tarifs']['normal'], 2) }} €</span>
                        </div>
                        <select name="places[normal]"
                                x-model="places.normal"
                                @change="updateTotal()"
                                class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 focus:border-gold focus:outline-none">
                            @for($i = 0; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'place' : 'places' }}</option>
                            @endfor
                        </select>
                    </div>
                    @endif

                    @if(isset($seance['tarifs']['reduit']))
                    <!-- Tarif Réduit -->
                    <div class="bg-gray-800 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-white font-medium">Tarif Réduit <span class="text-sm text-gray-400">(Étudiants, chômeurs, cartes privilège)</span></label>
                            <span class="text-gold font-semibold">{{ number_format($seance['tarifs']['reduit'], 2) }} €</span>
                        </div>
                        <select name="places[reduit]"
                                x-model="places.reduit"
                                @change="updateTotal()"
                                class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 focus:border-gold focus:outline-none">
                            @for($i = 0; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'place' : 'places' }}</option>
                            @endfor
                        </select>
                    </div>
                    @endif

                    @if(isset($seance['tarifs']['enfant']))
                    <!-- Tarif Enfant -->
                    <div class="bg-gray-800 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-white font-medium">Tarif Enfant <span class="text-sm text-gray-400">(Moins de 12 ans)</span></label>
                            <span class="text-gold font-semibold">{{ number_format($seance['tarifs']['enfant'], 2) }} €</span>
                        </div>
                        <select name="places[enfant]"
                                x-model="places.enfant"
                                @change="updateTotal()"
                                class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 focus:border-gold focus:outline-none">
                            @for($i = 0; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'place' : 'places' }}</option>
                            @endfor
                        </select>
                    </div>
                    @endif
                </div>

                <div class="mt-4 p-3 bg-gray-700 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-300">Total places sélectionnées:</span>
                        <span class="text-white font-semibold" x-text="totalPlaces"></span>
                    </div>
                    <p class="mt-2 text-sm text-gray-400">
                        {{ $seance['places_disponibles'] }} places disponibles pour cette séance
                    </p>
                </div>
            </div>

            <!-- Informations utilisateur -->
            @auth
            <div class="bg-gray-900/95 backdrop-blur-sm rounded-xl p-6 border border-gray-800">
                <h3 class="text-xl font-semibold text-white mb-6">Vos informations</h3>
                <div class="text-center">
                    <p class="text-gray-300">Connecté en tant que :</p>
                    @if(auth()->user()->credential)
                        <p class="text-gold font-semibold text-lg mt-2">
                            {{ auth()->user()->profil->nom ?? 'Utilisateur' }} {{ auth()->user()->profil->prenom ?? '' }}
                        </p>
                        <p class="text-gray-400">{{ auth()->user()->credential->email }}</p>
                    @else
                        <p class="text-gold font-semibold text-lg mt-2">Utilisateur connecté</p>
                        <p class="text-gray-400">ID: {{ auth()->user()->id }}</p>
                    @endif
                </div>
            </div>
            @endauth

            @guest
            <div class="bg-gray-900/95 backdrop-blur-sm rounded-xl p-6 border border-gray-800">
                <h3 class="text-xl font-semibold text-white mb-6">Connexion obligatoire</h3>

                <div class="text-center space-y-6">
                    <div>
                        <p class="text-gray-300 mb-2">Vous devez avoir un compte pour réserver</p>
                        <p class="text-gray-500 text-sm">Connectez-vous ou créez votre compte pour continuer</p>
                    </div>

                    <div class="flex gap-4 justify-center">
                        <a href="{{ route('login') }}?redirect={{ urlencode(request()->fullUrl()) }}"
                           class="px-8 py-3 bg-gold text-black font-semibold rounded-lg hover:bg-yellow-500 transition-colors inline-flex items-center gap-2">
                           <x-eos-login class="w-5 h-5" />
                            Se connecter
                        </a>
                        <a href="{{ route('register') }}?redirect={{ urlencode(request()->fullUrl()) }}"
                           class="px-8 py-3 border-2 border-gold text-gold hover:bg-gold hover:text-black rounded-lg font-semibold transition-colors inline-flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Créer un compte
                        </a>
                    </div>

                    <div class="bg-yellow-900/20 border border-yellow-600/30 rounded-lg p-4">
                        <div class="flex items-center gap-2 text-yellow-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">Information</span>
                        </div>
                        <p class="text-yellow-300 text-sm mt-2">
                            Un compte est nécessaire pour gérer vos réservations, recevoir vos billets par email et accéder à votre historique.
                        </p>
                    </div>
                </div>
            </div>
            @endguest

            <!-- Places sélectionnées (pour places numérotées) -->
            @if($seance['salle_type'] === 'numerotee')
            <div x-show="selectedSeats.length > 0" class="bg-gray-900/95 backdrop-blur-sm rounded-xl p-6 border border-gray-800">
                <h3 class="text-lg font-semibold text-white mb-4 text-center">Places sélectionnées</h3>
                <div class="flex justify-center flex-wrap gap-2">
                    <template x-for="seat in selectedSeats" :key="seat">
                        <span class="bg-gold text-black px-3 py-1 rounded-full font-medium text-sm" x-text="seat"></span>
                    </template>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="flex gap-4 justify-center">
                <a href="{{ url()->previous() }}"
                   class="px-8 py-3 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-500 rounded-lg font-medium transition-colors">
                    Retour
                </a>

                <button type="submit"
                        @guest disabled @endguest
                        :disabled="selectedCount === 0 @guest || true @endguest"
                        :class="selectedCount === 0 @guest || true @endguest ? 'opacity-50 cursor-not-allowed' : 'hover:bg-yellow-500'"
                        class="px-8 py-3 bg-gold text-black font-medium rounded-lg transition-colors">
                    @auth
                        Confirmer la réservation
                    @else
                        Connexion requise
                    @endauth
                </button>
            </div>

            {{-- @guest
                <div class="bg-yellow-900/50 border border-yellow-600 text-yellow-200 px-4 py-3 rounded relative mb-4" role="alert">
                    <p class="font-bold">Connexion requise</p>
                    <p class="text-sm">Vous devez être connecté pour finaliser votre réservation.</p>
                    <div class="mt-3">
                        <a href="{{ route('login') }}" class="inline-block bg-cinema-gold text-black px-4 py-2 rounded hover:bg-yellow-500 transition mr-2">
                            Se connecter
                        </a>
                        <a href="{{ route('register') }}" class="inline-block border border-cinema-gold text-cinema-gold px-4 py-2 rounded hover:bg-cinema-gold hover:text-black transition">
                            Créer un compte
                        </a>
                    </div>
                </div>
            @endguest --}}

            @if ($errors->any())
                <div class="bg-red-600 border border-red-500 text-white px-4 py-3 rounded relative" role="alert">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
.text-gold {
    color: #d4af37;
}

.bg-gold {
    background-color: #d4af37;
}

.border-gold {
    border-color: #d4af37;
}

.focus\:border-gold:focus {
    border-color: #d4af37;
}

.focus\:ring-gold:focus {
    --tw-ring-color: #d4af37;
}

.hover\:bg-yellow-500:hover {
    background-color: #eab308;
}

/* Animation pour les checkboxes sélectionnées */
input[type="checkbox"]:checked + label {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.4);
}
</style>
@endpush

@push('scripts')
<script>
function reservationApp() {
    return {
        places: {
            normal: 0,
            reduit: 0,
            enfant: 0
        },
        tarifs: {
            normal: {{ $seance['tarifs']['normal'] ?? 12.50 }},
            reduit: {{ $seance['tarifs']['reduit'] ?? 9.50 }},
            enfant: {{ $seance['tarifs']['enfant'] ?? 7.50 }}
        },
        maxSeats: 10,

        init() {
            // Initialisation
            this.updateTotal();
        },

        get totalPlaces() {
            return parseInt(this.places.normal || 0) +
                   parseInt(this.places.reduit || 0) +
                   parseInt(this.places.enfant || 0);
        },

        get selectedCount() {
            return this.totalPlaces;
        },

        get totalPrice() {
            const total = (this.places.normal * this.tarifs.normal) +
                        (this.places.reduit * this.tarifs.reduit) +
                        (this.places.enfant * this.tarifs.enfant);
            return total.toFixed(2);
        },

        updateTotal() {
            // Vérifier le maximum
            if (this.totalPlaces > this.maxSeats) {
                alert(`Vous ne pouvez sélectionner que ${this.maxSeats} places maximum au total`);
                // Réinitialiser la dernière sélection
                return false;
            }
        },

        updatePrice() {
            // Alias pour compatibilité
            this.updateTotal();
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Validation du formulaire
    document.querySelector('form').addEventListener('submit', function(e) {
        // Récupérer les valeurs des sélections (avec vérification d'existence)
        const normalSelect = document.querySelector('select[name="places[normal]"]');
        const reduitSelect = document.querySelector('select[name="places[reduit]"]');
        const enfantSelect = document.querySelector('select[name="places[enfant]"]');

        const normal = normalSelect ? parseInt(normalSelect.value) || 0 : 0;
        const reduit = reduitSelect ? parseInt(reduitSelect.value) || 0 : 0;
        const enfant = enfantSelect ? parseInt(enfantSelect.value) || 0 : 0;

        const total = normal + reduit + enfant;

        if (total === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins une place');
            return;
        }

        if (total > 10) {
            e.preventDefault();
            alert('Maximum 10 places au total');
            return;
        }
    });
});
</script>
@endpush
