@if($hasResults)
    <div class="space-y-3">
        <p class="text-gray-400 text-sm mb-4">{{ $films->count() }} résultat(s) pour "<span class="text-gold">{{ $query }}</span>"</p>

        @foreach($films as $film)
            <a href="{{ route('films.show', $film->film_id) }}"
               class="flex items-start gap-4 p-3 bg-gray-800/50 rounded-lg hover:bg-gray-700/50 transition-all group border border-gray-700/50 hover:border-gold/30"
               onclick="document.querySelector('[data-hs-overlay=&quot;#search-modal&quot;]').click()">

                <!-- Miniature -->
                <div class="flex-shrink-0">
                    @if($film->affiche_url)
                        <img src="{{ $film->affiche_url }}"
                             alt="{{ $film->titre }}"
                             class="w-16 h-20 rounded-lg object-cover shadow-md">
                    @else
                        <div class="w-16 h-20 bg-gradient-to-br from-gray-700 to-gray-800 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Infos -->
                <div class="flex-1 min-w-0">
                    <h4 class="text-white font-semibold group-hover:text-gold transition-colors text-base mb-2 line-clamp-1">
                        {{ $film->titre }}
                    </h4>

                    <!-- Métadonnées -->
                    <div class="space-y-1">
                        @if($film->realisateur)
                            <p class="text-gray-400 text-sm">
                                <span class="text-gray-500">Réal.</span> {{ $film->realisateur }}
                            </p>
                        @endif

                        @if(!empty($film->acteurs))
                            <p class="text-gray-400 text-sm line-clamp-1">
                                <span class="text-gray-500">Avec</span> {{ implode(', ', $film->acteurs) }}
                            </p>
                        @endif

                        <div class="flex items-center gap-3 text-sm text-gray-500">
                            @if($film->duree_minutes)
                                <span>{{ $film->duree_minutes }}min</span>
                            @endif

                            @if($film->genre)
                                <span>{{ $film->genre }}</span>
                            @endif

                            @if($film->note_moyenne > 0)
                                <div class="flex items-center gap-1">
                                    <span class="text-gold">⭐</span>
                                    <span class="text-gold font-medium">{{ number_format($film->note_moyenne, 1) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Flèche -->
                <div class="flex-shrink-0 self-center">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-gold transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>
        @endforeach

        <!-- Voir plus -->
        <div class="mt-6 pt-4 border-t border-gray-700">
            <a href="{{ route('films.index') }}"
               class="flex items-center justify-center gap-2 text-gold hover:text-yellow-400 text-sm font-medium transition-colors py-2"
               onclick="document.querySelector('[data-hs-overlay=&quot;#search-modal&quot;]').click()">
                <span>Voir tous les films</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </a>
        </div>
    </div>
@else
    <div class="text-center py-12">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <h3 class="text-gray-300 font-medium mb-2">Aucun film trouvé</h3>
        <p class="text-gray-500 text-sm">Aucun résultat pour "<span class="text-gray-400">{{ $query }}</span>"</p>
        <p class="text-gray-600 text-xs mt-2">Essayez avec le titre, un réalisateur ou un acteur</p>
    </div>
@endif