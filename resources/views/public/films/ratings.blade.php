@extends('layouts.cinema')

@section('title', 'Avis - ' . $film->titre)

@section('content')
<div class="bg-black text-white min-h-screen">
    <!-- Header avec retour -->
    <div class="bg-gray-900 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <nav class="flex items-center gap-4 text-sm text-gray-300 mb-4">
                <a href="{{ route('films.index') }}" class="hover:text-gold transition-colors">Films</a>
                <span>›</span>
                <a href="{{ route('films.show', $film->film_id) }}" class="hover:text-gold transition-colors">{{ $film->titre }}</a>
                <span>›</span>
                <span class="text-gold">Avis spectateurs</span>
            </nav>

            <div class="flex flex-col md:flex-row gap-6 items-start">
                <!-- Affiche du film -->
                <div class="w-24 md:w-32 flex-shrink-0">
                    @if($film->affiche_url)
                        <img src="{{ $film->affiche_url }}"
                             alt="{{ $film->titre }}"
                             class="w-full rounded-lg shadow-lg">
                    @else
                        <div class="w-full aspect-[2/3] bg-gray-800 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Informations film -->
                <div class="flex-1">
                    <h1 class="text-2xl md:text-3xl font-bold text-gold mb-2">{{ $film->titre }}</h1>
                    <div class="flex flex-wrap items-center gap-4 text-gray-300 text-sm mb-4">
                        <span>{{ $film->duree }} min</span>
                        <span>{{ ucfirst($film->genre) }}</span>
                        <span class="bg-gray-800 px-2 py-1 rounded">{{ $film->classification }}</span>
                        @if($film->date_sortie)
                            <span>{{ $film->date_sortie->format('Y') }}</span>
                        @endif
                    </div>

                    <!-- Statistiques globales -->
                    <div class="flex items-center gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-gold">{{ number_format($statistiques['note_moyenne'], 1) }}</div>
                            <div class="flex justify-center text-gold text-sm mb-1">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($statistiques['note_moyenne']))
                                        ⭐
                                    @else
                                        ☆
                                    @endif
                                @endfor
                            </div>
                            <div class="text-xs text-gray-400">Note moyenne</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-white">{{ $statistiques['total_avis'] }}</div>
                            <div class="text-xs text-gray-400">Avis spectateurs</div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col gap-2">
                    <a href="{{ route('films.show', $film->film_id) }}"
                       class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition-colors text-center">
                        Retour au film
                    </a>
                    <a href="{{ route('films.seances', $film->film_id) }}"
                       class="bg-gold hover:bg-yellow-500 text-black px-4 py-2 rounded-lg text-sm font-medium transition-colors text-center">
                        Voir les séances
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Messages de succès -->
        @if(session('success'))
            <div class="bg-green-900/20 border border-green-700 text-green-100 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Formulaire d'ajout d'avis -->
            <div class="lg:col-span-1">
                <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 sticky top-4">
                    <h2 class="text-xl font-bold text-gold mb-4">Donnez votre avis</h2>

                    <form method="POST" action="{{ route('films.ratings.store', $film->film_id) }}" class="space-y-4">
                        @csrf

                        <!-- Note -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Votre note</label>
                            <div class="flex gap-1" x-data="{ rating: 0, hover: 0 }">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button"
                                            class="text-2xl transition-colors"
                                            :class="rating >= {{ $i }} || hover >= {{ $i }} ? 'text-gold' : 'text-gray-600'"
                                            @click="rating = {{ $i }}"
                                            @mouseenter="hover = {{ $i }}"
                                            @mouseleave="hover = 0">
                                        ⭐
                                    </button>
                                @endfor
                                <input type="hidden" name="note" x-model="rating" required>
                            </div>
                            @error('note')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nom -->
                        <div>
                            <label for="nom_utilisateur" class="block text-sm font-medium text-gray-300 mb-2">Votre nom</label>
                            <input type="text"
                                   name="nom_utilisateur"
                                   id="nom_utilisateur"
                                   value="{{ old('nom_utilisateur') }}"
                                   class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 focus:outline-none focus:border-gold"
                                   required>
                            @error('nom_utilisateur')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Votre email</label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   value="{{ old('email') }}"
                                   class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 focus:outline-none focus:border-gold"
                                   required>
                            <p class="text-xs text-gray-400 mt-1">Non publié, utilisé uniquement pour la modération</p>
                            @error('email')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Commentaire -->
                        <div>
                            <label for="commentaire" class="block text-sm font-medium text-gray-300 mb-2">Votre commentaire (optionnel)</label>
                            <textarea name="commentaire"
                                      id="commentaire"
                                      rows="4"
                                      placeholder="Partagez votre avis sur ce film..."
                                      class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 focus:outline-none focus:border-gold resize-none">{{ old('commentaire') }}</textarea>
                            @error('commentaire')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="w-full bg-gold hover:bg-yellow-500 text-black font-medium py-3 px-4 rounded-lg transition-colors">
                            Publier mon avis
                        </button>

                        <p class="text-xs text-gray-400 text-center">
                            Votre avis sera publié après modération
                        </p>
                    </form>
                </div>
            </div>

            <!-- Liste des avis -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Répartition des notes -->
                @if($statistiques['total_avis'] > 0)
                    <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-white mb-4">Répartition des notes</h3>
                        <div class="space-y-2">
                            @for($note = 5; $note >= 1; $note--)
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-gray-300 w-12">{{ $note }} ⭐</span>
                                    <div class="flex-1 bg-gray-800 rounded-full h-2">
                                        <div class="bg-gold h-2 rounded-full"
                                             style="width: {{ $statistiques['total_avis'] > 0 ? ($statistiques['repartition'][$note] / $statistiques['total_avis']) * 100 : 0 }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-400 w-8">{{ $statistiques['repartition'][$note] }}</span>
                                </div>
                            @endfor
                        </div>
                    </div>
                @endif

                <!-- Titre section avis -->
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-white">
                        Avis spectateurs ({{ $avis->count() }})
                    </h3>
                </div>

                <!-- Liste des avis -->
                @forelse($avis as $avisItem)
                    <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gold rounded-full flex items-center justify-center text-black font-bold">
                                    {{ substr($avisItem->nom_utilisateur, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium text-white">{{ $avisItem->nom_utilisateur }}</div>
                                    <div class="text-sm text-gray-400">
                                        {{ \Carbon\Carbon::parse($avisItem->date_creation)->locale('fr')->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="text-sm {{ $i <= $avisItem->note ? 'text-gold' : 'text-gray-600' }}">⭐</span>
                                @endfor
                                <span class="ml-2 text-sm font-medium text-gold">{{ $avisItem->note }}/5</span>
                            </div>
                        </div>

                        @if($avisItem->commentaire)
                            <div class="text-gray-300 leading-relaxed">
                                {{ $avisItem->commentaire }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-gray-900 border border-gray-800 rounded-lg p-8 text-center">
                        <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2m0 0v10a2 2 0 002 2h6a2 2 0 002-2V8m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2"></path>
                        </svg>
                        <h4 class="text-lg font-medium text-gray-400 mb-2">Aucun avis pour le moment</h4>
                        <p class="text-gray-500">Soyez le premier à donner votre avis sur ce film !</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush