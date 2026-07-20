@extends('layouts.cinema')

@section('title', 'Réservation')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
            Réservez vos <span class="text-gold">places</span>
        </h1>
        <p class="text-xl text-gray-300 max-w-2xl mx-auto">
            Découvrez nos films par cinéma et choisissez votre séance idéale
        </p>
    </div>

    <!-- Filtres -->
    <div class="bg-gray-900 rounded-xl p-6 mb-8 border border-gray-800">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Date</label>
                <input type="date"
                       class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent"
                       x-model="selectedDate"
                       @change="filterByDate()">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Cinéma</label>
                <select class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent"
                        x-model="selectedCinema"
                        @change="filterByCinema()">
                    <option value="">Tous les cinémas</option>
                    <template x-for="cinema in cinemas" :key="cinema.cinema_id">
                        <option :value="cinema.cinema_id" x-text="cinema.nom + ' - ' + cinema.ville"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Genre</label>
                <select class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-gold focus:border-transparent"
                        x-model="selectedGenre"
                        @change="filterByGenre()">
                    <option value="">Tous les genres</option>
                    <option value="action">Action</option>
                    <option value="comedie">Comédie</option>
                    <option value="drame">Drame</option>
                    <option value="horreur">Horreur</option>
                    <option value="romance">Romance</option>
                    <option value="science-fiction">Science-Fiction</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-gray-900 rounded-xl p-6 border border-gray-800 text-center">
            <div class="text-3xl font-bold text-gold mb-2">{{ $totalCinemas }}</div>
            <div class="text-gray-300">Cinémas</div>
        </div>
        <div class="bg-gray-900 rounded-xl p-6 border border-gray-800 text-center">
            <div class="text-3xl font-bold text-gold mb-2">{{ $totalFilms }}</div>
            <div class="text-gray-300">Films disponibles</div>
        </div>
        <div class="bg-gray-900 rounded-xl p-6 border border-gray-800 text-center">
            <div class="text-3xl font-bold text-gold mb-2" x-text="totalSeances">0</div>
            <div class="text-gray-300">Séances aujourd'hui</div>
        </div>
    </div>

    <!-- Films par Cinéma -->
    <div x-data="reservationApp()" x-init="init()">
        <template x-for="cinema in filteredCinemas" :key="cinema.cinema_id">
            <div class="mb-12">
                <!-- En-tête Cinéma -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-white" x-text="cinema.nom"></h2>
                        <p class="text-gray-400" x-text="cinema.ville"></p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-400">Films disponibles</div>
                        <div class="text-xl font-bold text-gold" x-text="cinema.films.length"></div>
                    </div>
                </div>

                <!-- Slider Films -->
                <div class="relative">
                    <!-- Navigation gauche -->
                    <button @click="scrollLeft(cinema.cinema_id)"
                            class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-gray-900 hover:bg-gray-800 border border-gray-700 rounded-full p-3 transition-colors">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>

                    <!-- Navigation droite -->
                    <button @click="scrollRight(cinema.cinema_id)"
                            class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-gray-900 hover:bg-gray-800 border border-gray-700 rounded-full p-3 transition-colors">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>

                    <!-- Container Films -->
                    <div :id="'slider-' + cinema.cinema_id"
                         class="flex gap-6 overflow-x-auto scroll-smooth px-12 py-4 scrollbar-hide">
                        <template x-for="film in cinema.films" :key="film.film_id">
                            <div class="flex-shrink-0 w-72">
                                <!-- Card Film -->
                                <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden hover:border-gold transition-all duration-300 hover:scale-105">
                                    <!-- Image Film -->
                                    <div class="aspect-[3/4] bg-gray-800 relative overflow-hidden">
                                        <template x-if="film.affiche_url">
                                            <img :src="film.affiche_url" :alt="film.titre" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!film.affiche_url">
                                            <div class="w-full h-full bg-gradient-to-br from-blue-900 to-purple-900 flex items-center justify-center">
                                                <div class="text-6xl opacity-50">🎬</div>
                                            </div>
                                        </template>

                                        <!-- Badge Nouveauté -->
                                        <template x-if="isNewRelease(film.date_sortie)">
                                            <div class="absolute top-3 right-3">
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-gold text-black">
                                                    Nouveauté
                                                </span>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Infos Film -->
                                    <div class="p-6">
                                        <h3 class="text-xl font-bold text-white mb-2 line-clamp-2" x-text="film.titre"></h3>
                                        <p class="text-gray-400 mb-3">
                                            <span x-text="film.genre ? film.genre.charAt(0).toUpperCase() + film.genre.slice(1) : 'Film'"></span>
                                            <template x-if="film.duree">
                                                <span> • <span x-text="film.duree"></span>min</span>
                                            </template>
                                        </p>

                                        <!-- Note et Classification -->
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center gap-1">
                                                <svg class="w-5 h-5 text-gold" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                                <span class="text-white font-medium" x-text="film.note_moyenne ? parseFloat(film.note_moyenne).toFixed(1) : 'N/A'"></span>
                                            </div>
                                            <span class="text-sm text-gray-500" x-text="film.classification || ''"></span>
                                        </div>

                                        <!-- Boutons Action -->
                                        <div class="space-y-3">
                                            <button @click="showSeances(film)"
                                                    class="w-full py-3 px-4 bg-gold hover:bg-yellow-500 text-black font-medium rounded-lg transition-colors">
                                                Voir les séances
                                            </button>
                                            <a :href="'/films/' + film.film_id"
                                               class="block w-full py-3 px-4 border border-gold text-gold hover:bg-gold hover:text-black text-center font-medium rounded-lg transition-colors">
                                                En savoir plus
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        <!-- Message si aucun résultat -->
        <div x-show="filteredCinemas.length === 0" class="text-center py-12">
            <div class="text-6xl mb-4">🎬</div>
            <h3 class="text-xl font-bold text-white mb-2">Aucun film trouvé</h3>
            <p class="text-gray-400">Essayez de modifier vos critères de recherche</p>
        </div>
    </div>
</div>

<!-- Modal Séances -->
<div id="seances-modal" class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto">
    <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-2xl sm:w-full m-3 sm:mx-auto">
        <div class="bg-gray-900 border border-gray-700 rounded-xl shadow-lg">
            <!-- Header -->
            <div class="flex justify-between items-center py-4 px-6 border-b border-gray-700">
                <h3 class="text-xl font-bold text-white" x-text="selectedFilm ? selectedFilm.titre : ''"></h3>
                <button type="button" class="text-gray-400 hover:text-white" data-hs-overlay="#seances-modal">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6">
                <div x-show="loadingSeances" class="text-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gold mx-auto"></div>
                    <p class="text-gray-400 mt-2">Chargement des séances...</p>
                </div>

                <div x-show="!loadingSeances && seances.length > 0" class="space-y-4">
                    <template x-for="seance in seances" :key="seance.seance_id">
                        <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-white font-medium" x-text="formatDate(seance.date_heure_debut)"></div>
                                    <div class="text-gray-400 text-sm" x-text="'Salle ' + (seance.salle || 'N/A')"></div>
                                </div>
                                <div class="text-right">
                                    <div class="text-gold font-bold" x-text="(seance.tarif || 12) + '€'"></div>
                                    <button @click="selectSeance(seance)"
                                            class="mt-2 px-4 py-2 bg-gold hover:bg-yellow-500 text-black text-sm font-medium rounded-lg transition-colors">
                                        Sélectionner
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <div x-show="!loadingSeances && seances.length === 0" class="text-center py-8">
                    <div class="text-4xl mb-4">🎭</div>
                    <h4 class="text-lg font-bold text-white mb-2">Aucune séance disponible</h4>
                    <p class="text-gray-400">Ce film n'a pas de séances programmées pour le moment.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function reservationApp() {
    return {
        cinemas: @json($cinemaFilms),
        filteredCinemas: [],
        selectedDate: '',
        selectedCinema: '',
        selectedGenre: '',
        selectedFilm: null,
        seances: [],
        loadingSeances: false,
        totalSeances: 0,

        init() {
            this.filteredCinemas = this.cinemas;
            this.calculateTotalSeances();
        },

        filterByDate() {
            this.applyFilters();
        },

        filterByCinema() {
            this.applyFilters();
        },

        filterByGenre() {
            this.applyFilters();
        },

        applyFilters() {
            this.filteredCinemas = this.cinemas.filter(cinema => {
                // Filtre par cinéma
                if (this.selectedCinema && cinema.cinema_id !== this.selectedCinema) {
                    return false;
                }

                // Filtre par genre
                if (this.selectedGenre) {
                    const hasGenre = cinema.films.some(film =>
                        film.genre && film.genre.toLowerCase() === this.selectedGenre.toLowerCase()
                    );
                    if (!hasGenre) return false;
                }

                return true;
            }).map(cinema => ({
                ...cinema,
                films: cinema.films.filter(film => {
                    // Filtre par genre
                    if (this.selectedGenre &&
                        (!film.genre || film.genre.toLowerCase() !== this.selectedGenre.toLowerCase())) {
                        return false;
                    }
                    return true;
                })
            })).filter(cinema => cinema.films.length > 0);
        },

        scrollLeft(cinemaId) {
            const slider = document.getElementById('slider-' + cinemaId);
            if (slider) {
                slider.scrollBy({ left: -300, behavior: 'smooth' });
            }
        },

        scrollRight(cinemaId) {
            const slider = document.getElementById('slider-' + cinemaId);
            if (slider) {
                slider.scrollBy({ left: 300, behavior: 'smooth' });
            }
        },

        async showSeances(film) {
            this.selectedFilm = film;
            this.loadingSeances = true;
            this.seances = [];

            // Ouvrir le modal
            window.HSOverlay.open(document.getElementById('seances-modal'));

            try {
                const response = await fetch(`/reservation/film/${film.film_id}/seances`);
                const data = await response.json();

                if (data.seances) {
                    this.seances = data.seances;
                }
            } catch (error) {
                console.error('Erreur lors du chargement des séances:', error);
            } finally {
                this.loadingSeances = false;
            }
        },

        selectSeance(seance) {
            // Rediriger vers la page de checkout avec les infos
            const params = new URLSearchParams({
                film_id: this.selectedFilm.film_id,
                seance_id: seance.seance_id,
                titre: this.selectedFilm.titre,
                date_heure: seance.date_heure_debut,
                salle: seance.salle || 'N/A',
                tarif: seance.tarif || 12
            });

            window.location.href = `/checkout?${params.toString()}`;
        },

        isNewRelease(dateString) {
            if (!dateString) return false;
            const filmDate = new Date(dateString);
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
            return filmDate >= thirtyDaysAgo;
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        calculateTotalSeances() {
            let total = 0;
            this.cinemas.forEach(cinema => {
                cinema.films.forEach(film => {
                    if (film.prochaines_seances) {
                        total += film.prochaines_seances.length;
                    }
                });
            });
            this.totalSeances = total;
        }
    }
}
</script>

<style>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
@endpush