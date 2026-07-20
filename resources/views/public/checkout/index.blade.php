@extends('layouts.cinema')

@section('title', 'Panier - Sélection des places')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(!$reservation && !isset($seance))
        <!-- Panier vide -->
        <div class="text-center py-16">
            <div class="bg-gray-900 rounded-xl p-8 border border-gray-800">
                <svg class="mx-auto h-16 w-16 text-gray-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11h8"/>
                </svg>
                <h2 class="text-2xl font-bold text-white mb-4">Votre panier est vide</h2>
                <p class="text-gray-300 mb-6">
                    Sélectionnez une séance pour commencer votre réservation
                </p>
                <div class="flex gap-4 justify-center">
                    <a href="{{ route('films.index') }}"
                       class="px-6 py-3 bg-gold text-black font-medium rounded-lg hover:bg-yellow-500 transition-colors">
                        Voir les films
                    </a>
                    <a href="{{ route('seances.index') }}"
                       class="px-6 py-3 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-500 rounded-lg font-medium transition-colors">
                        Voir les séances
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Panier avec réservation -->
        <div x-data="checkoutApp()" x-init="init()">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                    Sélectionnez vos <span class="text-gold">places</span>
                </h1>
                <p class="text-gray-300">Finalisez votre réservation pour {{ $reservation->film_info['titre'] ?? $seance['titre'] ?? 'Film' }}</p>
            </div>

            <!-- Messages de feedback -->
            @if(session('success'))
                <div class="bg-green-900 border border-green-700 text-green-300 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-900 border border-red-700 text-red-300 px-4 py-3 rounded-lg mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Infos séance -->
            <div class="bg-gray-900 rounded-xl p-6 mb-8 border border-gray-800">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-xl font-bold text-white mb-4">{{ $reservation->film_info['titre'] ?? $seance['titre'] ?? 'Film' }}</h2>
                        <div class="space-y-2 text-gray-300">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>{{ $reservation ? $reservation->date_seance?->format('d/m/Y à H:i') : \Carbon\Carbon::parse($seance['date_heure'])->format('d/m/Y à H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>{{ $reservation ? ($reservation->seance_info['salle'] ?? 'Salle non définie') : $seance['salle'] }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                <span>{{ $reservation ? number_format($reservation->tarif_unitaire, 2) : number_format($seance['tarif'], 2) }}€ par place</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-800 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-white mb-3">Récapitulatif</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between text-gray-300">
                                <span>Places sélectionnées:</span>
                                <span x-text="selectedSeats.length || {{ $reservation ? $reservation->nb_places : 0 }}"></span>
                            </div>
                            <div class="flex justify-between text-gray-300">
                                <span>Prix unitaire:</span>
                                <span>{{ $reservation ? number_format($reservation->tarif_unitaire, 2) : number_format($seance['tarif'], 2) }}€</span>
                            </div>
                            <div class="border-t border-gray-700 pt-2 mt-2">
                                <div class="flex justify-between text-xl font-bold text-gold">
                                    <span>Total:</span>
                                    <span x-text="(selectedSeats.length || {{ $reservation ? $reservation->nb_places : 0 }}) * {{ $reservation ? $reservation->tarif_unitaire : $seance['tarif'] }} + '€'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sélection des places -->
            <div class="bg-gray-900 rounded-xl p-6 mb-8 border border-gray-800">
                <h3 class="text-xl font-semibold text-white mb-6 text-center">Plan de la salle</h3>

                <!-- Écran -->
                <div class="text-center mb-8">
                    <div class="inline-block bg-gradient-to-r from-gold to-yellow-500 text-black px-8 py-2 rounded-lg font-semibold mb-2">
                        ÉCRAN
                    </div>
                    <p class="text-sm text-gray-400">Les meilleures places sont au centre</p>
                </div>

                <!-- Grille des places -->
                <div class="max-w-2xl mx-auto">
                    <!-- Légende -->
                    <div class="flex justify-center gap-6 mb-6 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-gray-600 rounded"></div>
                            <span class="text-gray-300">Disponible</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-gold rounded"></div>
                            <span class="text-gray-300">Sélectionnée</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-red-600 rounded"></div>
                            <span class="text-gray-300">Occupée</span>
                        </div>
                    </div>

                    <!-- Rangées -->
                    <div class="space-y-3">
                        <template x-for="(row, rowIndex) in seatMap" :key="'row-' + rowIndex">
                            <div class="flex justify-center items-center gap-2">
                                <!-- Lettre de rangée -->
                                <div class="w-8 text-center text-gold font-bold" x-text="String.fromCharCode(65 + rowIndex)"></div>

                                <!-- Places -->
                                <div class="flex gap-1">
                                    <template x-for="(seat, seatIndex) in row" :key="'seat-' + rowIndex + '-' + seatIndex">
                                        <button
                                            @click="toggleSeat(rowIndex, seatIndex)"
                                            :disabled="seat.status === 'occupied'"
                                            :class="{
                                                'bg-gray-600 hover:bg-gray-500': seat.status === 'available',
                                                'bg-gold': seat.status === 'selected',
                                                'bg-red-600 cursor-not-allowed': seat.status === 'occupied'
                                            }"
                                            class="w-8 h-8 rounded text-white text-sm font-medium transition-colors"
                                            :title="'Rangée ' + String.fromCharCode(65 + rowIndex) + ' - Place ' + (seatIndex + 1)">
                                            <span x-text="seatIndex + 1"></span>
                                        </button>
                                    </template>
                                </div>

                                <!-- Numéro de rangée (droite) -->
                                <div class="w-8 text-center text-gold font-bold" x-text="String.fromCharCode(65 + rowIndex)"></div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Places sélectionnées -->
                <div x-show="selectedSeats.length > 0" class="mt-8 text-center">
                    <p class="text-gray-300 mb-2">Places sélectionnées:</p>
                    <div class="flex justify-center flex-wrap gap-2">
                        <template x-for="seat in selectedSeats" :key="seat.id">
                            <span class="bg-gold text-black px-3 py-1 rounded-full font-medium" x-text="seat.label"></span>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-4 justify-center">
                <form method="DELETE" action="{{ route('checkout.clear') }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir vider votre panier ?')"
                            class="px-8 py-3 border border-red-600 text-red-400 hover:text-red-300 hover:border-red-500 rounded-lg font-medium transition-colors">
                        Vider le panier
                    </button>
                </form>

                <a href="{{ route('reservation.index') }}"
                   class="px-8 py-3 border border-gray-600 text-gray-300 hover:text-white hover:border-gray-500 rounded-lg font-medium transition-colors">
                    Retour
                </a>

                <form method="POST" action="{{ route('checkout.update') }}" x-ref="updateForm" style="display: none;">
                    @csrf
                    <input type="hidden" name="nb_places" x-model="selectedSeats.length">
                    <input type="hidden" name="places" x-model="JSON.stringify(selectedSeats.map(s => s.label))">
                </form>

                <button @click="proceedToPayment()"
                        :disabled="selectedSeats.length === 0"
                        :class="selectedSeats.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-yellow-500'"
                        class="px-8 py-3 bg-gold text-black font-medium rounded-lg transition-colors">
                    Continuer le paiement
                </button>
            </div>
        </div>
    @endif
</div>
@endsection

@if($reservation)
@push('scripts')
<script>
function checkoutApp() {
    return {
        seatMap: [],
        selectedSeats: [],
        reservation: @json($reservation),
        seance: @json($seance ?? null),

        init() {
            this.generateSeatMap();
            this.loadExistingSelection();
        },

        loadExistingSelection() {
            // Charger les places déjà sélectionnées depuis la réservation
            const existingPlaces = this.reservation.places_reservees || [];
            existingPlaces.forEach(place => {
                const row = place.charCodeAt(0) - 65; // A=0, B=1, etc.
                const seat = parseInt(place.substring(1)) - 1; // 1-indexed to 0-indexed

                if (this.seatMap[row] && this.seatMap[row][seat]) {
                    this.toggleSeat(row, seat);
                }
            });
        },

        generateSeatMap() {
            // Générer un plan de salle 8x12 (8 rangées, 12 places par rangée)
            const rows = 8;
            const seatsPerRow = 12;

            this.seatMap = [];

            for (let row = 0; row < rows; row++) {
                const rowSeats = [];
                for (let seat = 0; seat < seatsPerRow; seat++) {
                    // Simuler quelques places occupées aléatoirement
                    const isOccupied = Math.random() < 0.15; // 15% de chances d'être occupée

                    rowSeats.push({
                        status: isOccupied ? 'occupied' : 'available',
                        row: row,
                        seat: seat
                    });
                }
                this.seatMap.push(rowSeats);
            }
        },

        toggleSeat(rowIndex, seatIndex) {
            const seat = this.seatMap[rowIndex][seatIndex];

            if (seat.status === 'occupied') return;

            const seatId = `${rowIndex}-${seatIndex}`;
            const seatLabel = `${String.fromCharCode(65 + rowIndex)}${seatIndex + 1}`;

            if (seat.status === 'available') {
                // Limiter à 8 places maximum
                if (this.selectedSeats.length >= 8) {
                    alert('Vous ne pouvez sélectionner que 8 places maximum');
                    return;
                }

                seat.status = 'selected';
                this.selectedSeats.push({
                    id: seatId,
                    label: seatLabel,
                    row: rowIndex,
                    seat: seatIndex
                });
            } else {
                seat.status = 'available';
                this.selectedSeats = this.selectedSeats.filter(s => s.id !== seatId);
            }
        },

        async proceedToPayment() {
            if (this.selectedSeats.length === 0) {
                alert('Veuillez sélectionner au moins une place');
                return;
            }

            if (this.reservation) {
                // Cas panier existant : mettre à jour
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('nb_places', this.selectedSeats.length);
                formData.append('places', JSON.stringify(this.selectedSeats.map(s => s.label)));

                try {
                    const response = await fetch('{{ route("cart.update") }}', {
                        method: 'POST',
                        body: formData
                    });

                    if (response.ok) {
                        window.location.href = '{{ route("payment") }}';
                    } else {
                        alert('Erreur lors de la mise à jour du panier');
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Erreur technique. Veuillez réessayer.');
                }
            } else if (this.seance) {
                // Cas nouvelle séance : créer réservation
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('seance_id', this.seance.seance_id);
                formData.append('film_id', this.seance.film_id);
                formData.append('titre', this.seance.titre);
                formData.append('date_heure', this.seance.date_heure);
                formData.append('salle', this.seance.salle);
                formData.append('tarif', this.seance.tarif);
                formData.append('nb_places', this.selectedSeats.length);
                formData.append('places', JSON.stringify(this.selectedSeats.map(s => s.label)));

                try {
                    const response = await fetch('{{ route("checkout.add") }}', {
                        method: 'POST',
                        body: formData
                    });

                    if (response.ok) {
                        window.location.href = '{{ route("payment") }}';
                    } else {
                        alert('Erreur lors de l\'ajout au panier');
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Erreur technique. Veuillez réessayer.');
                }
            }
        }
    }
}
</script>
@endpush
@endif